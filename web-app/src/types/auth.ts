export type PasswordAccess = {
  email: string;
  password: string;
}

export type RegistrationAccess = {
  email: string;
  password: string;
  firstName: string;
  lastName: string;
}



export type User = {
  id: number;
  email: string;
  firstName: string;
  lastName: string;
}
