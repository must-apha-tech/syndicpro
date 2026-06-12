import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from 'chart.js';
import { useState, useEffect } from 'react';
import { Bar } from 'react-chartjs-2';
import api from '../services/api';
import styles from '../components/Dashboard/Dashboard.module.css';
import StatCard from '../components/Dashboard/StatCard';

// Simple currency formatter for Moroccan Dirham (MAD)
const formatCurrency = (value) => {
  if (value == null) return '';
  return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'MAD' }).format(value);
};

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { position: 'top' },
    title: { display: false },
  },
};

const DashboardPage = () => {
  const [stats, setStats] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
        const response = await api.get('/dashboard/summary');
        setStats(response.data.data);
      } catch (error) {
        console.error('Error fetching stats:', error);
      } finally {
        setLoading(false);
      }
    };
    fetchStats();
  }, []);

  const chartData = {
    labels: ['Jan', 'Féb', 'Mar', 'Avr', 'Mai', 'Juin'],
    datasets: [
      {
        label: 'Encaissements',
        data: [12000, 19000, 15000, 22000, 28000, 24000],
        backgroundColor: '#1a4a8a',
      },
      {
        label: 'Charges',
        data: [10000, 12000, 11000, 13000, 12000, 14000],
        backgroundColor: '#2e7d5e',
      },
    ],
  };

  if (loading) return <div className={styles.loading}>Chargement...</div>;

  return (
    <div className={styles.container}>
      <header className={styles.header}>
        <h1>Tableau de Bord</h1>
        <p>Bienvenue sur votre gestion SyndicPro</p>
      </header>

      <div className={styles.grid}>
        <StatCard 
          title="Résidences" 
          value={stats?.residences_count || 0} 
          icon="🏢" 
        />
        <StatCard 
          title="Lots Totaux" 
          value={stats?.lots_count || 0} 
          icon="🔑" 
        />
        <StatCard 
          title="Impayés" 
          value={formatCurrency(stats?.total_unpaid || 0)} 
          icon="⚠️" 
          trend="down"
          change={5.2}
        />
        <StatCard 
          title="Taux Recouvrement" 
          value={`${stats?.recovery_rate_percent || 0}%`} 
          icon="📈" 
          trend="up"
          change={2.1}
        />
      </div>

      <section className={styles.mainContent}>
        <div className={styles.chartContainer}>
          <h3>Aperçu des Flux Financiers</h3>
          <div className={styles.chart}>
            <Bar options={chartOptions} data={chartData} />
          </div>
        </div>
        
        <div className={styles.recentActivity}>
          <h3>Activités Récentes</h3>
          <ul className={styles.activityList}>
            <li>Paiement reçu - Lot A12 (Immeuble Atlas)</li>
            <li>Incident résolu - Ascenseur (Résidence Farah)</li>
            <li>Appel de fonds généré - Trimestre 2</li>
          </ul>
        </div>
      </section>
    </div>
  );
};

export default DashboardPage;
