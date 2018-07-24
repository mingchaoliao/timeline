import {RouterModule, Routes} from '@angular/router';
import {LoginComponent} from './login/login.component';
import {SignupComponent} from './signup/signup.component';

const ACCOUNT_ROUTES: Routes = [
  {path: 'login', component: LoginComponent},
  {path: 'signup', component: SignupComponent},
];

export const ACCOUNT_ROUTING = RouterModule.forChild(ACCOUNT_ROUTES);
