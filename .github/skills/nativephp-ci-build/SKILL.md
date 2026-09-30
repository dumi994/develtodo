---
name: nativephp-ci-build
description: "Configura CI/CD per app NativePHP + Laravel desktop (Windows, macOS, Linux). Usa quando: build fallisce in CI, errore #plugin, JS error all avvio, setup CI/CD per NativePHP, creare release multi-piattaforma."
user-invocable: true
---

# NativePHP Desktop: CI/CD Build & Release

## When to Use

- Build fallisce in CI con errore `#plugin` o `ERR_INVALID_ARG_TYPE`
- Errore JS all'avvio dell'app desktop (`paths[1] must be string`)
- Configurare CI/CD per app NativePHP + Laravel
- Creare release multi-piattaforma (Windows, macOS, Linux)
- `php.exe` corrotto nella build locale o CI

## Prerequisites

- Laravel + `nativephp/electron` installed (`composer require nativephp/electron`)
- `php artisan native:install --publish` eseguito localmente
- GitHub repo con `contents: write` permissions

## Procedure

### 1. Fix `.env.production`

**File:** `nativephp/electron/.env.production`

```
MAIN_VITE_NATIVEPHP_BUILD_PATH=../../../build
```

**Niente apici** — `'../../../build'` causa `ERR_INVALID_ARG_TYPE`.

**Assicurati che NON sia in `.gitignore`.** Aggiungi nel root `.gitignore`:

```
.env
.env.backup
.env.production
!nativephp/electron/.env.production
!nativephp/electron/.env.development
```

### 2. Fix `electron-builder.mjs`

**File:** `nativephp/electron/electron-builder.mjs`

```js
afterSign: undefined,
win: {
    executableName: fileName,
    signtoolOptions: null,
    signAndEditExecutable: false,
},
```

In CI: `CSC_IDENTITY_AUTO_DISCOVERY: "false"`

### 3. GitHub Actions Workflow

**File:** `.github/workflows/build-release.yml`

#### Trigger

```yaml
on:
  push:
    branches: [dev]
    tags: ["v*"]
  workflow_dispatch:
```

#### Build electron plugin (ALL jobs — dist/ non è tracciato da git)

```yaml
- name: Build electron plugin (TypeScript)
  run: |
    cd nativephp/electron/electron-plugin
    npm install --silent 2>/dev/null || true
    npx tsc --project tsconfig.json 2>&1 || true
    cd ../../..
```

Per Windows (PowerShell):

```yaml
- name: Build electron plugin (TypeScript)
  shell: pwsh
  run: |
    Set-Location nativephp\electron\electron-plugin
    npm install --silent 2>&1 | Out-Null
    npx tsc --project tsconfig.json 2>&1 | Out-Null
    Set-Location ..\..\..
```

#### Build command — usa `--no-interaction` + OS arg

```yaml
- name: Build native app (Windows x64)
  env:
    CSC_IDENTITY_AUTO_DISCOVERY: "false"
  run: php artisan native:build win --no-interaction

- name: Build native app (macOS arm64)
  run: php artisan native:build mac --no-interaction

- name: Build native app (Linux x64)
  run: php artisan native:build linux --no-interaction
```

**NO pipe stdin** (`"0\n0" | ...` o `printf ... |`) — non funzionano in CI.

#### Upload artifact — tutta la cartella `dist/`

```yaml
- name: Upload artifact
  uses: actions/upload-artifact@v4
  with:
    name: develtodo-windows
    path: nativephp/electron/dist/
    retention-days: 30
```

#### Release job (solo su tag)

```yaml
release:
  if: startsWith(github.ref, 'refs/tags/')
  needs: [build-windows, build-macos, build-linux]
  runs-on: ubuntu-latest
  steps:
    - uses: actions/download-artifact@v4
      with:
        path: dist/
        merge-multiple: true
    - run: find dist/ -type f | sort
    - uses: softprops/action-gh-release@v2
      with:
        files: |
          dist/*.exe
          dist/*.dmg
          dist/*.AppImage
          dist/*.deb
          dist/*.zip
        draft: false
        prerelease: false
        fail_on_unmatched_files: false
        generate_release_notes: true
```

## Known Pitfalls

| Problema              | Sintomo                                         | Causa                                         | Fix                                                            |
| --------------------- | ----------------------------------------------- | --------------------------------------------- | -------------------------------------------------------------- |
| JS error all'avvio    | `ERR_INVALID_ARG_TYPE: paths[1] must be string` | `.env.production` con apici o in `.gitignore` | Togli apici, aggiungi al tracking                              |
| `#plugin` non risolto | `Rollup failed to resolve import "#plugin"`     | `electron-plugin/dist/` non in git            | Compila con `npx tsc` prima della build                        |
| Build bloccata        | Mostra prompt interattivi                       | Pipe stdin non funziona in CI                 | Usa `native:build win --no-interaction`                        |
| Niente setup.exe      | Solo zip nella release                          | Step zip custom salta gli installer           | Upload l'intera cartella `dist/`                               |
| `php.exe` corrotto    | 65,667,100 byte invece di 65,738,752            | Estrazione tronca il file                     | Estrai da `vendor/nativephp/php-bin/bin/win/x64/php-8.x.zip`   |
| Code signing bloccato | Errore firma                                    | Nessun certificato valido                     | `CSC_IDENTITY_AUTO_DISCOVERY: "false"`, `afterSign: undefined` |
| macOS runner arch     | `macos-latest` è arm64                          | GitHub default                                | Artifact: `*-macos-arm64*`, build: `native:build mac`          |

## Release Flow

```bash
git tag v1.0.x && git push origin v1.0.x
```

Per ritaggare dopo fix:

```bash
git tag -d v1.0.x && git push origin --delete v1.0.x
git tag v1.0.x && git push origin v1.0.x
```
