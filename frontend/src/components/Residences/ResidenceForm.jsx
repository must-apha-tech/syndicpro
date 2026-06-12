import React from 'react';
import { useForm } from 'react-hook-form';
import styles from './Residences.module.css';

const ResidenceForm = ({ onSubmit, initialData, onCancel, isLoading }) => {
  const { register, handleSubmit, formState: { errors } } = useForm({
    defaultValues: initialData || {
      name: '',
      address: '',
      city: '',
      nb_lots: 1,
      email: '',
      phone: '',
    }
  });

  return (
    <form onSubmit={handleSubmit(onSubmit)} className={styles.form}>
      <div className={styles.formGrid}>
        <div className={styles.formField}>
          <label>Nom de la Résidence</label>
          <input 
            {...register('name', { required: 'Le nom est requis' })} 
            placeholder="ex: Résidence Atlas"
          />
          {errors.name && <span className={styles.errorText}>{errors.name.message}</span>}
        </div>

        <div className={styles.formField}>
          <label>Ville</label>
          <input 
            {...register('city', { required: 'La ville est requise' })} 
            placeholder="ex: Casablanca"
          />
          {errors.city && <span className={styles.errorText}>{errors.city.message}</span>}
        </div>

        <div className={styles.formFieldFull}>
          <label>Adresse</label>
          <textarea 
            {...register('address', { required: 'L\'adresse est requise' })} 
            placeholder="Adresse complète..."
          />
        </div>

        <div className={styles.formField}>
          <label>Nombre de lots</label>
          <input 
            type="number" 
            {...register('nb_lots', { valueAsNumber: true, min: 1 })} 
          />
        </div>

        <div className={styles.formField}>
          <label>Téléphone Contact</label>
          <input {...register('phone')} />
        </div>
      </div>

      <div className={styles.formActions}>
        <button type="button" onClick={onCancel} className={styles.cancelBtn}>Annuler</button>
        <button type="submit" className={styles.submitBtn} disabled={isLoading}>
          {isLoading ? 'Enregistrement...' : 'Enregistrer'}
        </button>
      </div>
    </form>
  );
};

export default ResidenceForm;
