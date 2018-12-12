import { Component, OnInit, Injectable, Inject } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { ApiService } from '../../services/api/api.service';
import { environment } from '../../../environments/environment';

import { MatSnackBar, MatSnackBarHorizontalPosition, MatSnackBarVerticalPosition } from '@angular/material';
import { Router } from '@angular/router';

import { CookieService } from 'ngx-cookie-service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [ApiService]
})

@Injectable()
export class LoginComponent implements OnInit {
  loginForm: FormGroup;
  submitted = false;

  horizontalPosition: MatSnackBarHorizontalPosition = 'right';
  verticalPosition: MatSnackBarVerticalPosition = 'top';
  snackBarSettings = {
    duration: 5000,
    panelClass: ['snackbar-dark'],
    horizontalPosition: this.horizontalPosition,
    verticalPosition: this.verticalPosition
  };

  constructor(private router: Router,
    private formBuilder: FormBuilder,
    private apiService: ApiService,
    private snackBar: MatSnackBar,
    private cookieService: CookieService
  ) {

      this.loginForm = this.formBuilder.group({
        username: ['', Validators.required],
        password: ['', Validators.required]
      });
  }

  ngOnInit() {
    if (this.cookieService.get('userToken') != '') {
      this.snackBar.open('Du er allerede logget ind!', 'Luk', this.snackBarSettings);
      this.router.navigate(['/calendar']);
    }
  }

  // convenience getter for easy access to form fields
  get f() { return this.loginForm.controls; }

  onSubmit(){
    this.submitted = true;

    if (this.loginForm.invalid) {
      return;
    }

    const username = this.f.username.value;
    const password = this.f.password.value;

    const creds = btoa(username + ':' + password);

    this.apiService.auth(creds).subscribe((res) => {
      if (res && res['status'] === 200) {
        this.snackBar.open(res['msg'], 'Luk', this.snackBarSettings);

        let token = res['data']['credentials'];

        //Store cookie for 1 year
        const expDate = new Date();
        expDate.setFullYear(expDate.getFullYear() + 1);

        //Store in cookies instead
        //this.cookieService.set('userToken', encodeURI(token), expDate, '/', environment.productionUrl, environment.sslEnabled, 'Strict');
        this.cookieService.set('userToken', encodeURIComponent(token), expDate, '/', null, environment.sslEnabled, 'Strict');

        console.log('Set cookie');

        this.router.navigate(['/calendar']);
      } else {
        this.snackBar.open(res['msg'] || 'Fejl', 'Luk', this.snackBarSettings);
        this.submitted = true;
      }
    }, (res) => {
      this.snackBar.open(res.error.msg || 'Fejl', 'Luk', this.snackBarSettings);
      this.submitted = true;
    });
  }
}
