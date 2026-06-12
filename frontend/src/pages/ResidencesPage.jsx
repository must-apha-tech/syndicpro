import React, { useState, useEffect } from 'react';
import api from '../services/api';
import ResidenceList from '../components/Residences/ResidenceList';
import styles from '../components/Residences/Residences.module.css';

const ResidencesPage = () => {
  const [residences, setResidences] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const fetchResidences = async () => {
    setLoading(true);
    try {
      const response = await api.get('/residences');
      setResidences(response.data.data.data || response.data.data); // Handle pagination structure
    } catch (err) {
      setError('Impossible de charger les résidences');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchResidences();
  }, []);

  const handleCreate = () => {
    // Logic to open modal
    console.log('Open create modal');
  };

  const handleEdit = (id) => {
    console.log('Edit residence', id);
  };

  const handleDelete = async (id) => {
    if (window.confirm('Êtes-vous sûr de vouloir supprimer cette résidence ?')) {
      try {
        await api.delete(`/residences/${id}`);
        fetchResidences();
      } catch (err) {
        alert('Erreur lors de la suppression');
      }
    }
  };

  return (
    <div className={styles.pageContainer}>
      <header className={styles.pageHeader}>
        <h1>Gestion des Résidences</h1>
        <p>Gérez vos immeubles et copropriétés ici.</p>
      </header>

      {error && <div className={styles.errorBanner}>{error}</div>}

      <ResidenceList 
        residences={residences} 
        isLoading={loading}
        onCreateClick={handleCreate}
        onEditClick={handleEdit}
        onDeleteClick={handleDelete}
      />
    </div>
  );
};

export default ResidencesPage;
