import { panelHeading } from './panelHeading.js';
export class chartCard extends HTMLElement {
    connectedCallback() {

        

      this.getData();


/*       const myChart = new  Chart(canvas, {
            type: type,
            data: {
            labels: [transactionsGraph[0].month, transactionsGraph[1].month, transactionsGraph[2].month, transactionsGraph[3].month, transactionsGraph[4].month, transactionsGraph[5].month],
            datasets: [
                {
                label: "Transactions total",
                backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850","#3e95cd"],
                data: [transactionsGraph[0].balance, transactionsGraph[1].balance, transactionsGraph[2].balance, transactionsGraph[3].balance, transactionsGraph[4].balance, transactionsGraph[5].balance]
                }
            ]
            },
            options: {
            responsive: false,
            legend: { display: false },
            title: {
                display: true,
                text: 'Transactions total per month.'
            }
            }
        }); */

        /* const myChart = new Chart(canvas, {
            type: type,
            data: {
                labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
                datasets: [{
                    label: '# of Votes',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        }); */
    }

    getData(){
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
    };

    renderStat(transactionsGraph){

        const title = this.getAttribute('data-title');
        const type = this.getAttribute('data-chartType');
        const pheading = ` <panel-heading data-title="${title}"></panel-heading>`;
        const chartContainer = document.createElement('div');
        chartContainer.setAttribute('class','chart-container');
        const canvas = document.createElement('canvas');
        chartContainer.appendChild(canvas);
        console.log(chartContainer);
        this.innerHTML = pheading;
        this.appendChild(chartContainer);

        const myChart = new  Chart(canvas, {
            type: type,
            data: {
            labels: [transactionsGraph[0].month, transactionsGraph[1].month, transactionsGraph[2].month, transactionsGraph[3].month, transactionsGraph[4].month, transactionsGraph[5].month],
            datasets: [
                {
                label: "Transactions total",
                backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'],
                
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1,
                data: [transactionsGraph[0].balance, transactionsGraph[1].balance, transactionsGraph[2].balance, transactionsGraph[3].balance, transactionsGraph[4].balance, transactionsGraph[5].balance]
                }
            ]
            },
            options: {                
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

  }
      
  customElements.define('chart-card', chartCard);