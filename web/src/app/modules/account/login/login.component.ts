import {Component, EventEmitter} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {Router} from '@angular/router';
import {UserService} from '../../core/shared/services/user.service';
import {NotificationEmitter} from '../../core/shared/events/notificationEmitter';
import {Notification} from '../../core/shared/models/notification';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  public loginForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private userService: UserService,
    private router: Router
  ) {
    this.loginForm = formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]]
    });
  }

  public onSubmit(loading: EventEmitter<boolean>) {
    if (this.loginForm.valid) {
      loading.emit(true);
      this.userService.login(
        this.loginForm.value.email,
        this.loginForm.value.password
      ).subscribe(
        user => {
          loading.emit(false);
          this.router.navigate(['/']);
        },
        error => {
          loading.emit(false);
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to sign in'));
        }
      );
    }
  }
}
