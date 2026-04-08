import { Component } from '@angular/core';
import { SidebarComponent } from '../sidebar/sidebar.component';
import { ContentComponent } from '../content/content.component';
import { RouterOutlet } from "@angular/router";
import { CategoryComponent } from './category/category.component';

@Component({
  selector: 'app-page',
  imports: [SidebarComponent, RouterOutlet],
  templateUrl: './page.component.html',
  styleUrl: './page.component.css'
})
export class PageComponent {

}
