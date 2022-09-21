import { panelHeading } from './panelHeading.js';
export class maintTable extends HTMLElement {
    connectedCallback() {

        
      const title = this.getAttribute('data-title');
      const type = this.getAttribute('data-chartType');
      const pheading = ` <panel-heading data-title="${title}"></panel-heading>`;
      const divContainer = document.createElement('div');
      const tableWrapper = document.createElement('div');
      const tableContainer = document.createElement('div');
      
      divContainer.innerHTML = `<header class="header-triple">
                                <form action="./" method="post" class="form-inline">
                                    <p style="z-index: 1;">
                                        <label for="fta">Group by:</label>
                                        <span class="select"><select name="fta" id="mgrouping">
                                                                <option value="0">None</option>
                                                                <option value="1">Property</option>
                                                                <option value="2">Status</option>
                                                                <option value="3">Type</option>
                                                                <option value="4">Assigned to</option>
                                                                <option value="5">Priority</option>
                                                            </select></span>
                                    </p>
                                </form>
                                <p><a id="addMaintenanceButton" data-url = "maintenance/getModal?property_id=<?php echo $property->id?>" href="#maintenanceModal"><i class="fas fa-plus"></i> <span class="text-uppercase">New</span> Ticket</a></p>
                                </header>`;
      tableContainer.setAttribute('class','maintenance-tbl slick-tbl');
      tableWrapper.setAttribute('class','table-f-wrapper vtab div1');
      divContainer.setAttribute('class','maintenance-page');
      tableWrapper.appendChild(tableContainer);
      divContainer.appendChild(tableWrapper);

      this.innerHTML = pheading;
      this.appendChild(divContainer);
     const slick = new SlickMaintenance(tableContainer, {nofilter:true, dataUrl: "maintenance/getTickets?property_id=459"});
     $(tableContainer).data('slickgrid', slick);
    }
  }
      
  customElements.define('maint-table', maintTable);