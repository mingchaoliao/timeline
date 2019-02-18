import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';
import {NgbModule} from '@ng-bootstrap/ng-bootstrap';

import {AppComponent} from './app.component';
import {APP_ROUTING} from './app.route';
import {HomeModule} from './modules/home/home.module';
import {JwtModule} from '@auth0/angular-jwt';
import {CoreModule} from './modules/core/core.module';
import {HttpClient, HttpClientModule} from '@angular/common/http';
import {environment} from '../environments/environment';
import {AdminGuard} from './admin-guard';
import {EditorGuard} from './editor-guard';
import {AuthGuard} from './auth-guard';
import {TranslateLoader, TranslateModule} from '@ngx-translate/core';
import {TranslateHttpLoader} from '@ngx-translate/http-loader';

export function tokenGetter() {
  return localStorage.getItem('access_token');
}

export function HttpLoaderFactory(http: HttpClient) {
  return new TranslateHttpLoader(http);
}

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    BrowserModule,
    APP_ROUTING,
    NgbModule.forRoot(),
    HomeModule,
    HttpClientModule,
    JwtModule.forRoot({
      config: {
        headerName: 'Authorization',
        tokenGetter: tokenGetter,
        whitelistedDomains: environment.whitelistedDomains,
        authScheme: 'Bearer ',
        skipWhenExpired: true
      }
    }),
    TranslateModule.forRoot({
      loader: {
        provide: TranslateLoader,
        useFactory: HttpLoaderFactory,
        deps: [HttpClient]
      }
    }),
    CoreModule
  ],
  providers: [
    AdminGuard,
    EditorGuard,
    AuthGuard
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
}
