import { Component, OnInit, Input, ViewEncapsulation } from '@angular/core';

@Component({
  selector: 'app-loader',
  template: `
  <div class="app-loading">
      <svg class="spinner" viewBox="25 25 50 50" [style.width]="width" [style.height]="height" [style.padding]="padding">
        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4" stroke-miterlimit="10" [style.stroke]="color"/>
      </svg>
  </div>
  `,
  styles: [`.app-loading {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
  }

  .app-loading .spinner {
    height: 100%;
    width: 100%;
    display:flex;
    animation: rotate 2s linear infinite;
    transform-origin: center center;
    box-sizing: border-box;
  }
  
  .app-loading .spinner .path {
    stroke-dasharray: 1, 200;
    stroke-dashoffset: 0;
    animation: dash 1.5s ease-in-out infinite;
    stroke-linecap: round;
    stroke: #3f51b5;
  }
  
  @keyframes rotate {
    100% {
      transform: rotate(360deg);
    }
  }
  
  @keyframes dash {
    0% {
      stroke-dasharray: 1, 200;
      stroke-dashoffset: 0;
    }
  
    50% {
      stroke-dasharray: 89, 200;
      stroke-dashoffset: -35px;
    }
  
    100% {
      stroke-dasharray: 89, 200;
      stroke-dashoffset: -124px;
    }
  }`],
  encapsulation: ViewEncapsulation.Native
})
export class LoaderComponent implements OnInit {
  @Input() width;
  @Input() height;
  @Input() padding = 0;
  @Input() color = '#3f51b5';

  constructor() { }

  ngOnInit() {
  }

}
