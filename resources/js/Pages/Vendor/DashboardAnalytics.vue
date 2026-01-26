<template>
    <div class="vendor-dashboard">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-content">
                <div>
                    <h1>{{ store.name }}</h1>
                    <p class="subdomain">{{ store.subdomain }}</p>
                </div>
                <div class="header-actions">
                    <button @click="refreshData" class="btn btn-secondary">
                        <i class="icon-refresh"></i> تحديث
                    </button>
                    <button @click="exportDatabase" class="btn btn-primary">
                        <i class="icon-download"></i> تصدير قاعدة البيانات
                    </button>
                </div>
            </div>
        </div>

        <!-- Issues/Warnings Section -->
        <div v-if="issues.length > 0" class="issues-section">
            <h2>⚠️ التنبيهات والمشاكل</h2>
            <div class="issues-grid">
                <div v-for="issue in issues" :key="issue.title" :class="['issue-card', `severity-${issue.severity}`]">
                    <div class="issue-header">
                        <span class="severity-badge" :class="`badge-${issue.severity}`">
                            {{ issue.severity }}
                        </span>
                        <h3>{{ issue.title }}</h3>
                    </div>
                    <p>{{ issue.message }}</p>
                    <a :href="issue.link" class="issue-action">{{ issue.action }} →</a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="statistics-section">
            <h2>📊 الإحصائيات الرئيسية</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon orders">📦</div>
                    <div class="stat-content">
                        <h4>إجمالي الطلبيات</h4>
                        <p class="stat-value">{{ statistics.total_orders }}</p>
                        <span class="stat-pending" v-if="statistics.pending_orders > 0">
                            {{ statistics.pending_orders }} قيد الانتظار
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon revenue">💰</div>
                    <div class="stat-content">
                        <h4>إجمالي الإيرادات</h4>
                        <p class="stat-value">{{ statistics.total_revenue }}</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon products">🏷️</div>
                    <div class="stat-content">
                        <h4>المنتجات</h4>
                        <p class="stat-value">{{ statistics.total_products }}</p>
                        <span class="stat-subtitle">
                            {{ statistics.active_products }} نشط
                        </span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon rating">⭐</div>
                    <div class="stat-content">
                        <h4>متوسط التقييم</h4>
                        <p class="stat-value">{{ statistics.average_rating }}</p>
                        <span class="stat-subtitle">
                            من {{ statistics.total_reviews }} تقييم
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-section">
            <div class="chart-container">
                <h3>📈 الإيرادات خلال الفترة</h3>
                <canvas id="revenueChart"></canvas>
            </div>

            <div class="chart-container">
                <h3>📊 الطلبيات خلال الفترة</h3>
                <canvas id="ordersChart"></canvas>
            </div>
        </div>

        <!-- Top Products -->
        <div class="top-products-section">
            <h2>🔥 أفضل المنتجات</h2>
            <div class="table-container">
                <table class="products-table">
                    <thead>
                        <tr>
                            <th>المنتج</th>
                            <th>عدد الطلبيات</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in charts.top_products" :key="product.id">
                            <td>{{ product.name }}</td>
                            <td class="center">{{ product.orders_count }}</td>
                            <td class="center">
                                <span class="badge badge-success">نشط</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="performance-section">
            <h2>📉 مقاييس الأداء</h2>
            <div class="performance-grid">
                <div class="performance-card">
                    <h4>إكتمال المنتجات</h4>
                    <div class="progress-bar">
                        <div class="progress-fill" :style="{ width: (performance.product_completion || 0) + '%' }">
                        </div>
                    </div>
                    <p>{{ performance.product_completion || 0 }}%</p>
                </div>

                <div class="performance-card">
                    <h4>تحسين SEO</h4>
                    <div class="progress-bar">
                        <div class="progress-fill"
                            :style="{ width: (performance.seo_optimization?.total_score || 0) + '%' }">
                        </div>
                    </div>
                    <p>{{ performance.seo_optimization?.total_score || 0 }}%</p>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="activities-section">
            <div class="activity-column">
                <h3>🆕 آخر الطلبيات</h3>
                <div class="activity-list">
                    <div v-for="order in recentActivities.recent_orders" :key="order.id" class="activity-item">
                        <div class="activity-badge">📦</div>
                        <div class="activity-content">
                            <p class="activity-title">طلبية #{{ order.id }}</p>
                            <p class="activity-time">{{ formatDate(order.created_at) }}</p>
                        </div>
                        <span :class="['status-badge', 'status-' + order.status]">
                            {{ translateStatus(order.status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="activity-column">
                <h3>⭐ آخر التقييمات</h3>
                <div class="activity-list">
                    <div v-for="review in recentActivities.recent_reviews" :key="review.id" class="activity-item">
                        <div class="activity-badge">{{ review.rating }}⭐</div>
                        <div class="activity-content">
                            <p class="activity-title">{{ review.product.name }}</p>
                            <p class="activity-time">{{ formatDate(review.created_at) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="activity-column">
                <h3>🆕 آخر المنتجات</h3>
                <div class="activity-list">
                    <div v-for="product in recentActivities.recent_products" :key="product.id" class="activity-item">
                        <div class="activity-badge">🏷️</div>
                        <div class="activity-content">
                            <p class="activity-title">{{ product.name }}</p>
                            <p class="activity-time">{{ formatDate(product.created_at) }}</p>
                        </div>
                        <span :class="['status-badge', product.is_active ? 'status-active' : 'status-inactive']">
                            {{ product.is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Logs -->
        <div class="logs-section">
            <h2>📝 سجلات النظام</h2>
            <div class="logs-filters">
                <select v-model="logsFilter" class="filter-select">
                    <option value="all">جميع السجلات</option>
                    <option value="error">أخطاء</option>
                    <option value="warning">تنبيهات</option>
                    <option value="info">معلومات</option>
                </select>
            </div>
            <div class="logs-container">
                <div v-for="(log, index) in logs" :key="index" class="log-entry">
                    {{ log }}
                </div>
            </div>
        </div>

        <!-- Database Migration Status -->
        <div class="migration-section">
            <h2>🗄️ حالة قاعدة البيانات</h2>
            <div class="migration-info">
                <div class="info-item">
                    <label>حجم قاعدة البيانات:</label>
                    <span>{{ migrationStatus.database_size }}</span>
                </div>
                <div class="info-item">
                    <label>المنتجات:</label>
                    <span>{{ migrationStatus.product_count }}</span>
                </div>
                <div class="info-item">
                    <label>الطلبيات:</label>
                    <span>{{ migrationStatus.order_count }}</span>
                </div>
                <div class="info-item">
                    <label>المساحة المستخدمة:</label>
                    <span>{{ migrationStatus.storage_used }}</span>
                </div>
                <div class="info-item" v-if="migrationStatus.last_backup">
                    <label>آخر نسخة احتياطية:</label>
                    <span>{{ migrationStatus.last_backup }}</span>
                </div>
            </div>
            <button @click="exportDatabase" class="btn btn-primary btn-large">
                <i class="icon-download"></i> تنزيل قاعدة البيانات
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    storeId: {
        type: Number,
        required: true,
    },
});

const store = ref({});
const statistics = ref({});
const charts = ref({});
const recentActivities = ref({});
const issues = ref([]);
const performance = ref({});
const logs = ref([]);
const logsFilter = ref('all');
const migrationStatus = ref({});

let revenueChart = null;
let ordersChart = null;

onMounted(() => {
    loadDashboardData();
    loadLogs();
    loadMigrationStatus();
});

const loadDashboardData = async () => {
    try {
        const response = await fetch(
            `/vendor/dashboard/analytics?store_id=${props.storeId}`
        );
        const data = await response.json();

        store.value = data.store;
        statistics.value = data.statistics;
        charts.value = data.charts;
        recentActivities.value = data.recent_activities;
        issues.value = data.issues;
        performance.value = data.performance;

        initializeCharts();
    } catch (error) {
        console.error('Failed to load dashboard data:', error);
    }
};

const loadLogs = async () => {
    try {
        const response = await fetch(
            `/vendor/dashboard/logs?store_id=${props.storeId}&type=${logsFilter.value}`
        );
        const data = await response.json();
        logs.value = data.logs;
    } catch (error) {
        console.error('Failed to load logs:', error);
    }
};

const loadMigrationStatus = async () => {
    try {
        const response = await fetch(
            `/vendor/dashboard/migration-status?store_id=${props.storeId}`
        );
        const data = await response.json();
        migrationStatus.value = data;
    } catch (error) {
        console.error('Failed to load migration status:', error);
    }
};

const initializeCharts = () => {
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx && charts.value.revenue_chart) {
        if (revenueChart) revenueChart.destroy();
        revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: charts.value.revenue_chart.labels,
                datasets: [
                    {
                        label: 'الإيرادات',
                        data: charts.value.revenue_chart.data,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: { font: { size: 12 } },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    }

    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx && charts.value.orders_chart) {
        if (ordersChart) ordersChart.destroy();
        ordersChart = new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels: charts.value.orders_chart.labels,
                datasets: [
                    {
                        label: 'عدد الطلبيات',
                        data: charts.value.orders_chart.data,
                        backgroundColor: '#3b82f6',
                        borderColor: '#1e40af',
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: { font: { size: 12 } },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                },
            },
        });
    }
};

const refreshData = () => {
    loadDashboardData();
    loadLogs();
    loadMigrationStatus();
};

const exportDatabase = async () => {
    try {
        const response = await fetch(
            `/vendor/dashboard/export?store_id=${props.storeId}`,
            { method: 'GET' }
        );

        if (!response.ok) throw new Error('Export failed');

        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `store_${store.value.subdomain}_${new Date().toISOString().split('T')[0]}.sql`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
    } catch (error) {
        console.error('Failed to export database:', error);
        alert('فشل تصدير قاعدة البيانات');
    }
};

const formatDate = (date) => {
    return new Date(date).toLocaleString('ar-EG', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const translateStatus = (status) => {
    const statuses = {
        pending: 'قيد الانتظار',
        processing: 'قيد المعالجة',
        completed: 'مكتملة',
        cancelled: 'ملغاة',
    };
    return statuses[status] || status;
};
</script>

<style scoped>
.vendor-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
    background: #f8fafc;
    direction: rtl;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.header-content h1 {
    margin: 0;
    font-size: 2.5rem;
}

.subdomain {
    margin: 5px 0 0 0;
    opacity: 0.9;
    font-size: 1rem;
}

.header-actions {
    display: flex;
    gap: 15px;
}

/* Issues Section */
.issues-section {
    margin-bottom: 30px;
}

.issues-section h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.issues-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.issue-card {
    padding: 15px;
    border-radius: 8px;
    border-left: 5px solid;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.issue-card.severity-error {
    border-color: #ef4444;
    background: rgba(239, 68, 68, 0.05);
}

.issue-card.severity-warning {
    border-color: #f59e0b;
    background: rgba(245, 158, 11, 0.05);
}

.issue-card.severity-info {
    border-color: #3b82f6;
    background: rgba(59, 130, 246, 0.05);
}

.issue-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.issue-header h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #1f2937;
}

.severity-badge {
    font-size: 0.75rem;
    padding: 4px 8px;
    border-radius: 4px;
    text-transform: uppercase;
    font-weight: 600;
}

.badge-error {
    background: #fee2e2;
    color: #991b1b;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-info {
    background: #dbeafe;
    color: #0c2d6b;
}

.issue-card p {
    margin: 0 0 10px 0;
    color: #6b7280;
    font-size: 0.95rem;
}

.issue-action {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s;
}

.issue-action:hover {
    color: #1e40af;
}

/* Statistics Section */
.statistics-section {
    margin-bottom: 30px;
}

.statistics-section h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s, box-shadow 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    font-size: 2.5rem;
    min-width: 60px;
    text-align: center;
}

.stat-content h4 {
    margin: 0 0 5px 0;
    font-size: 0.95rem;
    color: #6b7280;
}

.stat-value {
    margin: 0;
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-subtitle,
.stat-pending {
    font-size: 0.85rem;
    color: #9ca3af;
    margin-top: 5px;
}

.stat-pending {
    color: #f59e0b;
    font-weight: 500;
}

/* Charts Section */
.charts-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.chart-container {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.chart-container h3 {
    margin: 0 0 15px 0;
    color: #1f2937;
}

canvas {
    max-height: 300px;
}

/* Top Products */
.top-products-section {
    margin-bottom: 30px;
}

.top-products-section h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.table-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.products-table {
    width: 100%;
    border-collapse: collapse;
}

.products-table thead {
    background: #f3f4f6;
}

.products-table th {
    padding: 12px;
    text-align: right;
    font-weight: 600;
    color: #1f2937;
    border-bottom: 2px solid #e5e7eb;
}

.products-table td {
    padding: 12px;
    border-bottom: 1px solid #e5e7eb;
    color: #4b5563;
}

.products-table tbody tr:hover {
    background: #f9fafb;
}

.products-table .center {
    text-align: center;
}

.badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.badge-success {
    background: #d1fae5;
    color: #065f46;
}

/* Performance Section */
.performance-section {
    margin-bottom: 30px;
}

.performance-section h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.performance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.performance-card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.performance-card h4 {
    margin: 0 0 15px 0;
    color: #1f2937;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s;
}

.performance-card p {
    margin: 0;
    color: #6b7280;
    font-weight: 500;
}

/* Activities Section */
.activities-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.activity-column {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.activity-column h3 {
    margin: 0 0 15px 0;
    font-size: 1.1rem;
    color: #1f2937;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    transition: background 0.3s;
}

.activity-item:hover {
    background: #f3f4f6;
}

.activity-badge {
    min-width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border-radius: 8px;
    font-size: 1.2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.activity-content {
    flex: 1;
}

.activity-title {
    margin: 0;
    font-weight: 500;
    color: #1f2937;
}

.activity-time {
    margin: 5px 0 0 0;
    font-size: 0.85rem;
    color: #9ca3af;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #fee2e2;
    color: #991b1b;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

/* Logs Section */
.logs-section {
    margin-bottom: 30px;
}

.logs-section h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.logs-filters {
    margin-bottom: 15px;
}

.filter-select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 0.95rem;
}

.logs-container {
    background: #1f2937;
    color: #e5e7eb;
    padding: 15px;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    font-size: 0.85rem;
    max-height: 400px;
    overflow-y: auto;
    direction: ltr;
}

.log-entry {
    padding: 5px 0;
    border-bottom: 1px solid #374151;
}

.log-entry:last-child {
    border-bottom: none;
}

/* Migration Section */
.migration-section {
    margin-bottom: 30px;
}

.migration-section h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #1f2937;
}

.migration-info {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-bottom: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e5e7eb;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 500;
    color: #6b7280;
}

.info-item span {
    color: #1f2937;
    font-weight: 600;
}

/* Buttons */
.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1rem;
    width: 100%;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-header {
        padding: 20px;
    }

    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }

    .header-content h1 {
        font-size: 1.8rem;
    }

    .header-actions {
        width: 100%;
        flex-direction: column;
    }

    .btn {
        width: 100%;
        justify-content: center;
    }

    .stats-grid,
    .charts-section,
    .performance-grid,
    .activities-section,
    .issues-grid {
        grid-template-columns: 1fr;
    }

    .logs-container {
        max-height: 250px;
    }
}
</style>
