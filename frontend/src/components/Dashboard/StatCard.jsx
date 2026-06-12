import React from 'react';
import styles from './Dashboard.module.css';

const StatCard = ({ title, value, icon, trend, change }) => (
  <div className={styles.statCard}>
    <div>
      <div className={styles.statTitle}>{title}</div>
      <div className={styles.statValue}>{value}</div>
      {trend && (
        <div className={`${styles.statChange} ${styles[trend]}`}>
          {change}% {trend === 'up' ? '▲' : '▼'}
        </div>
      )}
    </div>
    <div className={styles.statIcon}>{icon}</div>
  </div>
);

export default StatCard;
