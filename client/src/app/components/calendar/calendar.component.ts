import { Component, OnInit, ViewChild } from '@angular/core';
import { ApiService } from '../../services/api/api.service';

import { MatSnackBar, MatSnackBarHorizontalPosition, MatSnackBarVerticalPosition } from '@angular/material';

import { Router } from '@angular/router';
import { Observable } from 'rxjs';

import { CookieService } from 'ngx-cookie-service';

import * as moment from 'moment';

@Component({
  selector: 'app-calendar',
  templateUrl: './calendar.component.html',
  styleUrls: ['./calendar.component.scss'],
  providers: [ApiService]
})
export class CalendarComponent implements OnInit {
  horizontalPosition: MatSnackBarHorizontalPosition = 'right';
  verticalPosition: MatSnackBarVerticalPosition = 'top';
  snackBarSettings = {
    duration: 3500,
    panelClass: ['snackbar-dark'],
    horizontalPosition: this.horizontalPosition,
    verticalPosition: this.verticalPosition
  };

  loaded = false;

  apiError = false;
  calendarView = 'week';
  currentDate;

  prevBtnDisabled = true;
  nextBtnDisabled = true;

  currentWeekTimestamp;

  currentIndex = 0;
  data = [];
  calendar = [];

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

    this.apiService.date().subscribe(
      res => {
        this.currentDate = res['data'].datetime;
      },
      err => {
        this.apiError = true;

        this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);
      }
    );

    //Load initial calendar "current week"
    this.getCalendar().subscribe(
      res => {
        this.loaded = true;
        this.apiError = false;

        this.calendar.push(res);
        this.currentWeekTimestamp = res.timestamp;

        this.snackBar.open(res.msg, 'Luk', this.snackBarSettings);

        const startI = -1;
        const endI = 2;

        for (let i = startI; i <= endI; i++) {
          if (i === 0) continue;

          this.getCalendar(i).subscribe(
            res => {
              this.calendar.push(res);

              if (this.calendar.length === (Math.abs(startI) + Math.abs(endI) + 1)) {
                this.calendar.sort((a, b) => {
                  return a.timestamp - b.timestamp;
                });
                this.currentIndex = this.getCurrentWeekIndex();

                this.nextBtnDisabled = false;
                this.prevBtnDisabled = false;
              }
            },
            err => {
              this.apiError = true;

              this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);
            });
        }

        this.setCalendar();
      },
      err => {
        this.loaded = true;
        this.apiError = true;

        this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);
      }
    );
  }

  getCurrentWeekIndex() {
    return this.calendar.findIndex(x => x.timestamp == this.currentWeekTimestamp);
  }

  getCalendar(number = 0) {
    return Observable.create(observer => {

      this.apiService.week(number).subscribe(
        (date) => {
          this.apiService.calendar(date).subscribe(
            (res) => {
              let days = res['data'];

              const timestamp = date * 1000;

              for (let i = 0; i < days.length; i++) {
                const current = new Date(timestamp);
                current.setDate(current.getDate() + i);

                // 0 = monday
                days[i].map(el => {
                  const hourStart = Math.floor(el.time / 60);
                  const minStart = (el.time - (hourStart * 60));

                  const minEndCarry = Math.floor((minStart + (el.duration % 60)) / 60);
                  const minEnd = (minStart + (el.duration % 60)) % 60;
                  const hourEnd = hourStart + Math.floor(el.duration / 60) + minEndCarry;

                  const start = new Date();
                  start.setFullYear(current.getFullYear());
                  start.setMonth(current.getMonth());
                  start.setDate(current.getDate());
                  start.setHours(hourStart);
                  start.setMinutes(minStart);
                  start.setMinutes(0);

                  const end = new Date();
                  end.setFullYear(current.getFullYear());
                  end.setMonth(current.getMonth());
                  end.setDate(current.getDate());
                  end.setHours(hourEnd);
                  end.setMinutes(minEnd);
                  end.setMinutes(0);

                  el.start = start;
                  el.end = end;
                });
              }

              days = this.pivot(this.addCalendarHeaders(days));

              const isLeap = (year) => new Date(year, 1, 29).getDate() === 29;

              // NOTE:
              // This is a good enough algorithm for now,
              // but breaks in 2021, sooo proably should be rewritten.

              let firstDayOfWeekYear = moment(timestamp).format('Y');
              //sunday 01:00
              let timestampSunday = timestamp + (60*60*24*6 * 1000) + (60*60 * 1000);
              let lastDayOfWeekYear = moment(timestampSunday).format('Y');

              let heading;
              if ((firstDayOfWeekYear !== lastDayOfWeekYear) && !isLeap(firstDayOfWeekYear)) {
                heading = moment(timestampSunday).format('[Uge] w, MMMM - Y');
              } else {
                heading = moment(timestamp).format('[Uge] w, MMMM - Y');
              }

              return observer.next({
                timestamp: timestamp,
                title: heading,
                events: days,
                cached: res['cached'],
                msg: res['msg'],
                status: res['status']
              });
            },

            (res) => {
              return observer.error(res);
            }
          );
        },
        (res) => {
          return observer.error(res);
        }
      );
    });
  }

  setCalendar() {
    this.data = this.calendar[this.currentIndex];
  }

  prevCalendar() {
    if (this.prevBtnDisabled) return;

    this.currentIndex--;
    this.nextBtnDisabled = false;

    // We are at the end and are requesting a previous week
    // -1 because 0 is a valid index
    if (this.currentIndex === -1) {
      // Actually this is a fail-safe. You should only be able to be here if
      // prevBtn is not disabled, which is it is until it has loaded prev week.

      this.snackBar.open('Ups. Vi skal lige hente kalenderen', 'Luk', this.snackBarSettings);

      // We should be retrieving prev week soon because of next statement
      // will initiate API retrieval

    } else if (this.currentIndex === 0) {
      this.prevBtnDisabled = true;

      this.setCalendar();

      //Load prev week
      const currentWeekIndex = this.getCurrentWeekIndex();
      const prevWeek = -1 * Math.abs(currentWeekIndex - this.currentIndex) - 1;
      this.getCalendar(prevWeek).subscribe(
        res => {
          this.calendar.unshift(res);
          this.prevBtnDisabled = false;

          //Because we inserted element at pos: 0, we are now shifted 1 pos.
          this.currentIndex = this.currentIndex + 1;

        },
        err => {
          this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);
        }
      );
    } else {
      this.setCalendar();
    }
  }

  nextCalendar() {
    if (this.nextBtnDisabled) return;

    this.currentIndex++;
    this.prevBtnDisabled = false;

    // We are at the end and are requesting next week
    if (this.currentIndex === this.calendar.length) {
      // Actually this is a fail-safe. You should only be able to be here if
      // nextBtn is not disabled, which is it is until it has loaded next week.

      this.snackBar.open('Ups. Vi skal lige hente kalenderen', 'Luk', this.snackBarSettings);

      // We should be retrieving prev week soon because of next statement
      // will initiate API retrieval

    } else if (this.currentIndex === this.calendar.length - 1) {
      this.nextBtnDisabled = true;

      this.setCalendar();

      //Load next week
      const currentWeekIndex = this.getCurrentWeekIndex();
      const nextWeek = Math.abs(currentWeekIndex - this.currentIndex) + 1;
      this.getCalendar(nextWeek).subscribe(
        res => {
          this.calendar.push(res);

          this.nextBtnDisabled = false;

        },
        err => {
          this.snackBar.open('Fejl: ' + err.status + ' - ' + err.statusText, 'Luk', this.snackBarSettings);
        }
      );
    } else {
      this.setCalendar();
    }
  }

  addCalendarHeaders(events) {
    for (let i = 0; i < events.length; i++) {
      let first = events[i][0];

      if (first.special === true) {
        events.shift();

        first = events[i][0];
      }

      const dayStr = moment(first.start).format('ddd[.]');
      const dateStr = moment(first.start).format('D');

      const element = {
        special: true,
        day: i,
        date: first.start,
        heading: { day: dayStr[0].toUpperCase() + dayStr.substr(1), date: dateStr },
      };

      events[i] = [element, ...events[i]];
    }

    return events;
  }

  pivot(arr) {
    return arr[0].map((_, i) => arr.map(row => row[i]));
  }

}
