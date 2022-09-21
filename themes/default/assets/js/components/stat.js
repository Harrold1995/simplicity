export class statCard extends HTMLElement {
    connectedCallback() {
        this.getInfo();
    }

    getInfo() {
        return new Promise((res, rej) => {
          
        const url = this.getAttribute('data-source');
          fetch(JS.baseUrl+url)
            .then(data => data.json())
            .then((json) => {
              this.renderStat(json);
              res();
            })
            .catch((error) => rej(error));
        })
      }
  
      renderStat(data) {
        const title = this.getAttribute('data-title');
        const icon = this.getAttribute('data-icon');
        let num = data.num;
        this.innerHTML = `<div class="grid-stack-item stat-wrapper1" id="time" data-gs-x="3" data-gs-y="3" data-gs-width="3" data-gs-height="2">


                    <div class="stat-info-wrapper">
                        <span class="stat-number"><span class="counter-anim">${num}</span></span>
                        <span class="stat-name">${title}</span>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="${icon}"></i>
                    </div>
                </div>
            
            `;
      }
  }
      
  customElements.define('stat-card', statCard);