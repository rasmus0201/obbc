import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable  } from 'rxjs';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})

export class ApiService {
  //private url = 'http://165.227.174.67/api';
  private url = environment.productionApi;
  public token = null;

  constructor(protected http: HttpClient) { }

  public auth(creds) {
    const params = new HttpParams().set('token', creds);
    const endpoint = `${this.url}/user/auth`;

    return this.http.get(endpoint, { params });
  }

  public date() {
    const endpoint = `${this.url}/date`;

    return this.http.get(endpoint);
  }

  public bookings() {
    const params = new HttpParams().set('token', this.token);
    const endpoint = `${this.url}/user/bookings`;

    return this.http.get(endpoint, { params });
  }

  public subscribe(teamId) {
    const params = new HttpParams().set('token', this.token);
    const endpoint = `${this.url}/user/bookings/book/${teamId}`;

    return this.http.post(endpoint, null, { params });
  }

  public leave(bookingId) {
    const params = new HttpParams().set('token', this.token);
    const endpoint = `${this.url}/user/bookings/leave/${bookingId}`;

    return this.http.post(endpoint, null, { params });
  }

  public week(weeksFromNow = 0) {

    return Observable.create(observer => {
      this.date().subscribe(
        (res) => {
          let getMonday;
          if (res['status'] === 200) {
            const currentMonday = this.getMonday(Date.parse(res['data']['date']));
            getMonday = currentMonday;

            if (weeksFromNow < 0 || weeksFromNow > 0) {
              getMonday.setDate(currentMonday.getDate() + (weeksFromNow * 7));
            }
          } else {
            getMonday = null;
          }

          return observer.next(getMonday.getTime() / 1000);
        },
        (err) => {
          return observer.error(err);
        });
    });

  }

  public calendar(timestamp = null) {
    if (timestamp !== null) {
      timestamp = '/' + timestamp;
    }

    const params = new HttpParams().set('token', this.token);
    const endpoint = `${this.url}/user/calendar${timestamp}`;

    return this.http.get(endpoint, { params });
  }

  private getMonday(d) {
    d = new Date(d);
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1); // adjust when day is sunday

    return new Date(d.setDate(diff));
  }
}
