import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {Router} from '@angular/router';
import {UserService} from '../../core/shared/services/user.service';

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

  public onSubmit() {
    if (this.loginForm.valid) {
      this.userService.login(
        this.loginForm.value.email,
        this.loginForm.value.password
      ).subscribe(
        user => {
          this.router.navigate(['/']);
        },
        error => {
          console.log(error);
          // TODO: handle error
        }
      );
    }
  }

}
