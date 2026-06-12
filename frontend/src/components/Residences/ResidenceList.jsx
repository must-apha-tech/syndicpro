import React from 'react';
import { formatCurrency } from '../../utils/formatters';
import styles from './Residences.module.css';

const ResidenceList = ({ residences, onCreateClick, onEditClick, onDeleteClick, isLoading }) => {
  if (isLoading) return <div className={styles.loading}>Chargement des résidences...</div>;

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h3>Liste des Résidences</h3>
        <button onClick={onCreateClick} className={styles.createBtn}>+ Nouvelle Résidence</button>
      </div>

      <div className={styles.tableWrapper}>
        <table className={styles.table}>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Ville</th>
              <th>Lots</th>
              <th>Impayés</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {residences.length === 0 ? (
              <tr>
                <td colSpan="5" className={styles.empty}>Aucune résidence trouvée.</td>
              </tr>
            ) : (
              residences.map((res) => (
                <tr key={res.id}>
                  <td><strong>{res.name}</strong></td>
                  <td>{res.city}</td>
                  <td>{res.nb_lots}</td>
                  <td className={styles.unpaid}>{formatCurrency(res.unpaid_charges || 0)}</td>
                  <td className={styles.actions}>
                    <button onClick={() => onEditClick(res.id)} className={styles.editBtn}>Modifier</button>
                    <button onClick={() => onDeleteClick(res.id)} className={styles.deleteBtn}>Supprimer</button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
};

export default ResidenceList;
