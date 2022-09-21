export class panelHeading extends HTMLElement {
    connectedCallback() {

        
      const title = this.getAttribute('data-title');
      this.innerHTML = `<div class="panel-heading">
                            <div class = 'pull-left'><span>${title}</span></div>
                            <div class = 'pull-right'>
                                <div class = 'panel-heading-icons'>
                                <i class="icon-user"></i>
                                <i class="icon-user"></i>
                                <i class="fas fa-ellipsis-v"></i>
                            </div>
                            </div>
                        </div>`;

    }
  }
      
  customElements.define('panel-heading', panelHeading);