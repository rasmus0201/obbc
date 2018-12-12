import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../services/api/api.service';

import { MatSnackBar, MatSnackBarHorizontalPosition, MatSnackBarVerticalPosition } from '@angular/material';

import { Router } from '@angular/router';

import { CookieService } from 'ngx-cookie-service';

import * as moment from 'moment';

@Component({
  selector: 'app-bookings',
  templateUrl: './bookings.component.html',
  styleUrls: ['./bookings.component.scss'],
  providers: [ApiService]
})
export class BookingsComponent implements OnInit {

  horizontalPosition: MatSnackBarHorizontalPosition = 'right';
  verticalPosition: MatSnackBarVerticalPosition = 'top';
  snackBarSettings = {
    duration: 5000,
    panelClass: ['snackbar-dark'],
    horizontalPosition: this.horizontalPosition,
    verticalPosition: this.verticalPosition
  };

  bookings = [];
  loaded = false;
  apiError = false;

  constructor(
    private router: Router,
    private apiService: ApiService,
    private snackBar: MatSnackBar,
    private cookieService: CookieService
  ) {
    moment.locale('da-DK');

    this.apiService.token = this.cookieService.get('userToken');
  }

  ngOnInit() {
    if (this.cookieService.get('userToken') == '') {
      this.snackBar.open('Du er ikke logget ind!', 'Luk', this.snackBarSettings);
      this.router.navigate(['/login']).then(_ => { });

      return;
    }

    this.apiService.bookings().subscribe(
      res => {
        this.bookings = res['data'];
        this.snackBar.open(res['msg'], 'Luk', this.snackBarSettings);
        this.loaded = true;

        console.log(res);
      },
      err => {
        this.apiError = true;
        this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);
      }
    );
  }

  format(date) {
    return moment(date, 'DD/MM/YYYY').format('ddd [d.] Do MMM [-] YYYY');
  }

  unsubscribe(booking) {
    booking.disabled = true;

    // for some reason this call always return 404 - bug in API
    this.apiService.leave(booking.id).subscribe(
      res => {
        // console.log(res);

        this.remove(booking.id);
        this.snackBar.open('Du er blevet afmeldt holdet', 'Luk', this.snackBarSettings);
      },
      err => {
        // console.log(err);

        // this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);


        this.remove(booking.id);
        this.snackBar.open('Du er blevet afmeldt holdet', 'Luk', this.snackBarSettings);
      },
    );
  }

  remove(id) {
    for (const day of this.bookings) {
      day['bookings'] = day['bookings'].filter(function (value) {
        return value['id'] !== id;
      });
    }

    this.bookings = this.bookings.filter(function (value) {
      return value['bookings'].length > 0;
    });

  }

}
