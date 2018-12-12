import {Component, Input} from '@angular/core';

import * as moment from 'moment';
import {ApiService} from '../../services/api/api.service';
import {CookieService} from 'ngx-cookie-service';

@Component({
  selector: 'app-flexybox-calendar',
  templateUrl: './flexybox-calendar.component.html',
  styleUrls: ['./flexybox-calendar.component.scss'],
  providers: [ApiService]
})

export class FlexyboxCalendarComponent {
  @Input() view;
  @Input() data;
  @Input() currentDate;

  constructor(private apiService: ApiService, private cookieService: CookieService) {
    this.apiService.token = this.cookieService.get('userToken');
  }

  duration(min) {
    const duration = moment.duration(parseInt(min, 10), 'minutes');

    return duration.humanize();
  }

  subscribe(team) {
    if (team.disallowBooking === false && team.id !== undefined && this.currentDate !== undefined) {
      team.isLoading = true;

      // Do POST request.
      this.apiService.subscribe(team.id).subscribe(
        res => {
          team.isLoading = false;
          team.isJoined = true;
        },
        err => {
          team.isLoading = false;
        }
      );
    }
  }

  unsubscribeAllowed(team) {
    return team.isJoined && team.id !== undefined;
  }
}
