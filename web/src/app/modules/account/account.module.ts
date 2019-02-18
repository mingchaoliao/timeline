import {NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {CoreModule} from '../core/core.module';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';
import {LoginComponent} from './login/login.component';
import {SignupComponent} from './signup/signup.component';
import {ACCOUNT_ROUTING} from './account.route';
import {ProfileComponent} from './profile/profile.component';
import {TranslateModule} from "@ngx-translate/core";

@NgModule({
  imports: [
    CommonModule,
    CoreModule,
    FormsModule,
    ReactiveFormsModule,
    NgbModule,
    ACCOUNT_ROUTING,
    TranslateModule.forChild()
  ],
  declarations: [
    LoginComponent,
    SignupComponent,
    ProfileComponent
  ]
})
export class AccountModule {
}
