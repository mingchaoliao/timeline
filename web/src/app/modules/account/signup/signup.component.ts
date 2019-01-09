import {Component, EventEmitter} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {UserService} from '../../core/shared/services/user.service';
import {Router} from '@angular/router';
import {Notification} from '../../core/shared/models/notification';
import {NotificationEmitter} from '../../core/shared/events/notificationEmitter';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.component.html',
  styleUrls: ['./signup.component.css']
})
export class SignupComponent {

  public signupForm: FormGroup;

  constructor(
    private formBuilder: FormBuilder,
    private userService: UserService,
    private router: Router
  ) {
    this.signupForm = formBuilder.group({
      name: ['', [Validators.required]],
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required]]
    });
  }

  public onSubmit(loading: EventEmitter<boolean>) {
    if (this.signupForm.valid) {
      loading.emit(true);
      this.userService.register(
        this.signupForm.value.name,
        this.signupForm.value.email,
        this.signupForm.value.password
      ).subscribe(
        user => {
          loading.emit(false);
          this.router.navigate(['/']);
        },
        error => {
          loading.emit(false);
          NotificationEmitter.emit(Notification.error(error.error.message, 'Unable to sign up'));
        }
      );
    }
  }

}
