<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <div class="space-y-6" x-data="dashboardData()" x-init="initChart()">
        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">📁</span>
                    <span class="text-xs text-emerald-600 font-semibold" x-text="'+' + stats.newProjects"></span>
                </div>
                <p class="text-3xl font-extrabold" x-text="stats.activeProjects"></p>
                <p class="text-xs text-gray-400 mt-1">Progetti attivi</p>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">📋</span>
                    <span class="text-xs text-amber-600 font-semibold" x-text="stats.dueToday + ' oggi'"></span>
                </div>
                <p class="text-3xl font-extrabold" x-text="stats.openTasks"></p>
                <p class="text-xs text-gray-400 mt-1">Task aperti</p>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">🎫</span>
                    <span class="text-xs text-red-600 font-semibold" x-text="stats.highPriorityTickets + ' urgenti'"></span>
                </div>
                <p class="text-3xl font-extrabold" x-text="stats.openTickets"></p>
                <p class="text-xs text-gray-400 mt-1">Ticket aperti</p>
            </div>
            <div class="card p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-2xl">💰</span>
                    <span class="text-xs text-emerald-600 font-semibold" x-text="'€' + stats.totalUnpaid.toLocaleString()"></span>
                </div>
                <p class="text-3xl font-extrabold" x-text="stats.overdueInvoices"></p>
                <p class="text-xs text-gray-400 mt-1">Fatture in ritardo</p>
            </div>
        </div>

        <!-- Timeline + Quotes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 card p-5">
                <h3 class="font-bold text-sm mb-4 flex items-center gap-2">📅 Prossime scadenze</h3>
                <div class="relative pl-2">
                    <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    <div class="space-y-5">
                        <template x-for="item in upcomingDeadlines" :key="item.id">
                            <div class="relative pl-8">
                                <div class="absolute left-0 top-0.5 w-[23px] h-[23px] rounded-full border-4 border-white dark:border-gray-900 shadow-md" x-bind:style="'background:' + item.color"></div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-semibold" x-text="item.title"></p>
                                        <p class="text-xs text-gray-400" x-text="item.project"></p>
                                    </div>
                                    <span class="text-xs font-medium" x-bind:class="item.urgent ? 'text-red-500 pulse font-bold' : 'text-gray-400'" x-text="item.daysLeft"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            <div class="card p-5">
                <h3 class="font-bold text-sm mb-4 flex items-center gap-2">📄 Preventivi in attesa</h3>
                <div class="space-y-3">
                    <template x-for="p in pendingQuotes" :key="p.id">
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-sm font-semibold" x-text="p.client"></p>
                                <span class="badge" x-bind:class="p.expiring ? 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300'" x-text="p.status"></span>
                            </div>
                            <p class="text-xs text-gray-400" x-text="'€ ' + p.amount.toLocaleString() + ' · ' + p.waitingDays + ' giorni'"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="card p-5">
            <h3 class="font-bold text-sm mb-4">📊 Task completati (ultimi 7 giorni)</h3>
            <div style="height:200px"><canvas id="prodChart"></canvas></div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
    function dashboardData() {
        return {
            chartInstance: null,
            stats: { activeProjects:8, newProjects:2, openTasks:24, dueToday:5, openTickets:12, highPriorityTickets:4, overdueInvoices:3, totalUnpaid:12450 },
            upcomingDeadlines: [
                { id:1, title:'Fix header responsive', project:'Sito Web Agency', color:'#ef4444', daysLeft:'Oggi', urgent:true },
                { id:2, title:'Implementare checkout', project:'E-commerce', color:'#f59e0b', daysLeft:'Domani', urgent:true },
                { id:3, title:'Test invio email', project:'WebApp CRM', color:'#3b82f6', daysLeft:'3 giorni', urgent:false },
                { id:4, title:'Deploy staging', project:'Portfolio', color:'#10b981', daysLeft:'5 giorni', urgent:false },
                { id:5, title:'Collaudo finale', project:'Blog Plus', color:'#8b5cf6', daysLeft:'7 giorni', urgent:false }
            ],
            pendingQuotes: [
                { id:1, client:'Alessio Rinaldi', amount:3200, status:'In attesa', waitingDays:7, expiring:true },
                { id:2, client:'Tech Solutions', amount:5800, status:'In attesa', waitingDays:3, expiring:false },
                { id:3, client:'Studio Legale Bianchi', amount:2100, status:'In attesa', waitingDays:10, expiring:true }
            ],
            initChart() {
                var self = this;
                setTimeout(function() {
                    var ctx = document.getElementById('prodChart');
                    if (!ctx) return;
                    if (self.chartInstance) { self.chartInstance.destroy(); }
                    self.chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: { labels: ['Lun','Mar','Mer','Gio','Ven','Sab','Dom'], datasets: [{ label:'Task completati', data:[5,8,3,12,7,2,4], backgroundColor:'#3b82f6', borderRadius:6, maxBarThickness:40 }] },
                        options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false } }, scales:{ y:{ beginAtZero:true, grid:{ color:'rgba(148,163,184,0.1)' }, ticks:{ font:{ size:11 } } }, x:{ grid:{ display:false }, ticks:{ font:{ size:11 } } } } }
                    });
                }, 300);
            }
        }
    }
    </script>
    @endpush
</x-app-layout>