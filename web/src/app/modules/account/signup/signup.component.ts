import {Component} from '@angular/core';
import {FormBuilder, FormGroup, Validators} from '@angular/forms';
import {UserService} from '../../core/shared/services/user.service';
import {Router} from '@angular/router';

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

  public onSubmit() {
    if (this.signupForm.valid) {
      this.userService.register(
        this.signupForm.value.name,
        this.signupForm.value.email,
        this.signupForm.value.password
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
