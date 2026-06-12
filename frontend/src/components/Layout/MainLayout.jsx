import React from 'react';
import { Outlet, NavLink, useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import styles from './Layout.module.css';

const MainLayout = () => {
  const { user, logout } = useAuth();
  const navigate = useNavigate();

  const handleLogout = () => {
    logout();
    navigate('/login');
  };

  return (
    <div className={styles.layout}>
      <aside className={styles.sidebar}>
        <div className={styles.logo}>
          <h2>SyndicPro</h2>
        </div>
        
        <nav className={styles.nav}>
          <NavLink to="/dashboard" className={({ isActive }) => isActive ? styles.active : ''}>
            Dashboard
          </NavLink>
          <NavLink to="/residences" className={({ isActive }) => isActive ? styles.active : ''}>
            Résidences
          </NavLink>
          <NavLink to="/comptabilite" className={({ isActive }) => isActive ? styles.active : ''}>
            Comptabilité
          </NavLink>
          <NavLink to="/ag" className={({ isActive }) => isActive ? styles.active : ''}>
            Assemblées
          </NavLink>
          <NavLink to="/incidents" className={({ isActive }) => isActive ? styles.active : ''}>
            Incidents
          </NavLink>
        </nav>

        <div className={styles.sideFooter}>
          <p>{user?.name}</p>
          <button onClick={handleLogout} className={styles.logoutBtn}>Déconnexion</button>
        </div>
      </aside>

      <main className={styles.main}>
        <header className={styles.topHeader}>
          <div className={styles.search}>
            <input type="text" placeholder="Rechercher..." />
          </div>
          <div className={styles.userProfile}>
            <span>{user?.role === 'syndic' ? '👑' : '👤'}</span>
          </div>
        </header>

        <div className={styles.content}>
          <Outlet />
        </div>
      </main>
    </div>
  );
};

export default MainLayout;
