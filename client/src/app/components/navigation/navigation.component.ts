import { MediaMatcher } from '@angular/cdk/layout';
import { ChangeDetectorRef, Component, AfterViewInit, OnDestroy, Input, ViewChild } from '@angular/core';
import { BreakpointObserver } from '@angular/cdk/layout';
import { filter } from 'rxjs/operators';
import { Router, NavigationEnd } from '@angular/router';
import { MatSidenav } from '@angular/material';
import { CookieService } from 'ngx-cookie-service';
/** @title Responsive sidenav */
@Component({
  selector: 'app-main-navigation',
  templateUrl: 'navigation.component.html',
  styleUrls: ['navigation.component.css'],
})


export class NavigationComponent implements AfterViewInit, OnDestroy {
  token: string = this.cookieService.get('userToken');

  @ViewChild('snav') drawer: MatSidenav;

  mobileQuery: MediaQueryList;

  private _mobileQueryListener: () => void;

  constructor(
    changeDetectorRef: ChangeDetectorRef,
    media: MediaMatcher,
    private breakpointObserver: BreakpointObserver,
    private router: Router,
    private cookieService: CookieService
  ) {
    this.mobileQuery = media.matchMedia('(max-width: 600px)');
    this._mobileQueryListener = () => changeDetectorRef.detectChanges();
    this.mobileQuery.addListener(this._mobileQueryListener);
  }

  ngAfterViewInit() {
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe(() => {
      this.drawer.close();
      this.token = this.cookieService.get('userToken');
    });
  }

  ngOnDestroy(): void {
    this.mobileQuery.removeListener(this._mobileQueryListener);
  }

  home() {
    if (this.token !== null) {
      this.router.navigate(['/calendar']);
    } else {
      this.router.navigate(['/login']);
    }
  }

  logout() {
    this.cookieService.deleteAll();

    this.router.navigate(['/login']);
  }
}
