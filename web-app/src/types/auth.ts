// Définit le type pour l'accès de base (utilisé par login et resetPassword)
export type PasswordAccess = {
  email: string;
  password: string;
}

// Définit le type pour l'enregistrement, incluant les noms
export type RegistrationAccess = {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
}

// Les autres types de votre application
export type AuthResponse = {
  token: string;
  // ... autres champs
}

export type User = {
  data: {
    id: number;
    email: string;
    firstName: string;
    lastName: string;
    // ... autres propriétés utilisateur
  }
}
