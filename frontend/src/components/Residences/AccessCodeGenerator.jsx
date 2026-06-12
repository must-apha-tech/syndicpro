import React, { useState, useEffect } from 'react';
import axios from 'axios';
import styles from './AccessCodeGenerator.module.css';

const AccessCodeGenerator = ({ residenceId }) => {
  const [lots, setLots] = useState([]);
  const [loading, setLoading] = useState(true);
  const [codes, setCodes] = useState([]);
  const [selectedLot, setSelectedLot] = useState(null);
  const [generatedCode, setGeneratedCode] = useState(null);
  const [showModal, setShowModal] = useState(false);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchData();
  }, [residenceId]);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [lotsRes, codesRes] = await Promise.all([
        axios.get(`/api/residences/${residenceId}/lots`),
        axios.get(`/api/residences/${residenceId}/access-codes`)
      ]);
      setLots(lotsRes.data);
      setCodes(codesRes.data.data);
    } catch (err) {
      setError('Erreur lors du chargement des données');
    } finally {
      setLoading(false);
    }
  };

  const generateCode = async (lot) => {
    setSelectedLot(lot);
    setGeneratedCode(null);
    setShowModal(true);
    setError('');

    try {
      const response = await axios.post(`/api/residences/${residenceId}/lots/${lot.id}/access-code`);
      setGeneratedCode(response.data);
      // Refresh history
      fetchData();
    } catch (err) {
      setError(err.response?.data?.error || 'Erreur lors de la génération du code');
    }
  };

  const revokeCode = async (code) => {
    if (!window.confirm('Êtes-vous sûr de vouloir révoquer ce code ?')) return;

    try {
      await axios.delete(`/api/access-codes/${code}`);
      fetchData();
    } catch (err) {
      alert(err.response?.data?.error || 'Erreur lors de la révocation');
    }
  };

  const copyToClipboard = () => {
    if (generatedCode) {
      navigator.clipboard.writeText(generatedCode.code);
      alert('Code copié !');
    }
  };

  if (loading) return <div className={styles.loading}>Chargement...</div>;

  return (
    <div className={styles.container}>
      <header className={styles.header}>
        <h2>Gestion des Codes d'Accès</h2>
        <p>Générez des codes pour permettre l'inscription des copropriétaires</p>
      </header>

      {error && <div className={styles.errorAlert}>{error}</div>}

      <div className={styles.section}>
        <h3>Liste des Lots</h3>
        <div className={styles.tableWrapper}>
          <table className={styles.table}>
            <thead>
              <tr>
                <th>N° Lot</th>
                <th>Type</th>
                <th>Surface</th>
                <th>Propriétaire</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              {lots.map(lot => (
                <tr key={lot.id}>
                  <td>{lot.numero}</td>
                  <td>{lot.type}</td>
                  <td>{lot.surface} m²</td>
                  <td>{lot.proprietaire ? lot.proprietaire.name : <span className={styles.unassigned}>Non assigné</span>}</td>
                  <td>
                    <button 
                      className={styles.genButton}
                      onClick={() => generateCode(lot)}
                      disabled={lot.proprietaire}
                    >
                      Générer Code
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      <div className={styles.section}>
        <h3>Historique des codes</h3>
        <div className={styles.historyList}>
          {codes.map(codeItem => (
            <div key={codeItem.code} className={styles.codeRow}>
              <div className={styles.codeVal}>{codeItem.code}</div>
              <div className={styles.codeLot}>Lot {codeItem.lot.numero}</div>
              <div className={styles.codeMeta}>
                <span>Créé par: {codeItem.created_by}</span>
                <span>Expire le: {new Date(codeItem.expires_at).toLocaleDateString()}</span>
              </div>
              <div className={`${styles.status} ${styles[codeItem.status]}`}>
                {codeItem.status === 'used' ? `Utilisé par ${codeItem.used_by}` : codeItem.status}
              </div>
              {codeItem.status !== 'used' && (
                <button 
                  className={styles.revokeButton}
                  onClick={() => revokeCode(codeItem.code)}
                >
                  Révoquer
                </button>
              )}
            </div>
          ))}
        </div>
      </div>

      {showModal && (
        <div className={styles.modalOverlay}>
          <div className={styles.modal}>
            <h2>Code Généré</h2>
            {generatedCode ? (
              <div className={styles.modalContent}>
                <p>Code pour le lot <strong>{selectedLot.numero}</strong></p>
                <div className={styles.bigCode}>{generatedCode.code}</div>
                <div className={styles.expiration}>Valide jusqu'au {new Date(generatedCode.expires_at).toLocaleString()}</div>
                
                <div className={styles.modalActions}>
                  <button onClick={copyToClipboard} className={styles.actionBtn}>Copier</button>
                  <button onClick={() => window.print()} className={styles.actionBtn}>Imprimer</button>
                </div>
              </div>
            ) : (
              <div className={styles.loading}>Génération en cours...</div>
            )}
            <button className={styles.closeBtn} onClick={() => setShowModal(false)}>Fermer</button>
          </div>
        </div>
      )}
    </div>
  );
};

export default AccessCodeGenerator;
