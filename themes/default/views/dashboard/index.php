<style>
   .dashboard-root{
    height: auto !important;
    background: #fafafa4d;
   }

   .dashboard-root .maintenance-page{
     display:block;
     padding:0px;
   }
    .stats-wrapper{
        display:flex;
    }
   .stat-wrapper{
        height: 130px;
        width: 25%;
        text-align: center;
        border-radius: 0;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 20%), 0 1px 1px 0 rgb(0 0 0 / 14%), 0 2px 1px -1px rgb(0 0 0 / 12%);
        margin: 15px;
        background: white;
    }
    
    .stat-wrapper1{
        display: -webkit-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }
    
    .grid-stack-item{
        height: 100%; 
    }

    .grid-stack-item-content{
        border-radius: 10px;
        border: 1px solid #f2f2f2;
        background: white; 
        text-align:center;
    }

    .panel-wrapper{
        height: 430px;
        width: 37%;
        text-align: center;
        border-radius: 0;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 20%), 0 1px 1px 0 rgb(0 0 0 / 14%), 0 2px 1px -1px rgb(0 0 0 / 12%);
        margin: 15px;
        background: white;
    }

    .panel-wrapper-full-width{
        height: 430px;
        width: 100%;
        text-align: center;
        border-radius: 0;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 20%), 0 1px 1px 0 rgb(0 0 0 / 14%), 0 2px 1px -1px rgb(0 0 0 / 12%);
        margin: 15px;
        background: white;
    }
    .panel-wrapper-no-border{
        height: 440px;
        width: 23%;
        text-align: center;
    }

    .sub-panel{
        padding: 35px;
    }
    .sub-panel-wrapper{
        height: 47%;
        box-shadow: 0 1px 3px 0 rgb(0 0 0 / 20%), 0 1px 1px 0 rgb(0 0 0 / 14%), 0 2px 1px -1px rgb(0 0 0 / 12%);
        background: white;
        
        margin: 15px;
        display: block;

    }
    .stat-number{
        font-size: 24px;
        display: block;
    }
    .stat-name{
        font-size: 13px;
        font-weight: 600 !important;
        text-transform: uppercase;
        display: block;
        color: #878787;
    }

    #dashboard-wrapper{
        background: #fafafa;
    }
    .stat-info-wrapper{
        width:50%;
        display: inline-block;
    }
    .stat-icon-wrapper{
        width:50%;
        float: right;
        display: inline-block;
        font-size: 50px;
        color: #dedede;
    }
    .pink{
        color: #f473c4 !important;
    }

    .pink-icon i{
        color: #f473c4 !important;
    }

    .panel-heading{
        height:60px;
        background: #f2f2f2;
        padding:15px;
        font-weight: 500;
        font-size: 18px;

    }
    
    .pull-right{float:right} 
    .pull-left{float:left}
    .panel-heading-icons i{
        margin:5px;
        color: #878787;
        font-size: 15px;
    }
    .sub-panel-title{
        display: block;
        font-weight: 600 !important;
        text-transform: uppercase;
        font-size: 13px;

    }
    .sub-panel-number{
        display: block;
        font-weight: 600 !important;
        text-transform: uppercase;
        font-size: 45px;

    }

    .chart-container{
        padding:20px;

    }
    .slick-tbl{
        min-height:300px;
    }
    

    .pink-background, .pink-background .grid-stack-item, .pink-background .stat-name{
        background-color: #f473c4 !important;
        color: white !important;
    }
    .pink-background .grid-stack-item{
        background-color: #f473c4 !important;
        color: white !important;
    }

    .welcome-screen{
        height: 100vh;
        background-color: #f2f2f2;
        width: 100vw;
        overflow: visible;
        /*background-image: url(http://[::1]/Custom_styles/simplicity/themes/default/assets/images/welcome-bg.jpg);*/
        background-size: cover;
        background-position: bottom;
        margin-top: -120px;
       margin-left: -50px;
       /*background: linear-gradient(-45deg, #edafc7, #ffffff, #cbe1ed, #ffffff);
	background-size: 400% 400%;
	animation: gradient 15s ease infinite;*/
    }

    @keyframes gradient {
	0% {
		background-position: 0% 50%;
	}
	50% {
		background-position: 100% 50%;
	}
	100% {
		background-position: 0% 50%;
	}
}

   }
</style>

<script src="<?php echo base_url(); ?>themes/default/assets/js/plugins/gridstack-h5.js"></script>
<link href="<?php echo base_url(); ?>themes/default/assets/css/gridstack.min.css" rel="stylesheet"/>

<div class="welcome-screen">
    <h1 id = "greeting" style ="text-align: center; FONT-WEIGHT: 500; color: #white; margin-top: 10%;">Welcome Back, Harry</h1>
</div>
<div class="grid-stack">
</div>
<button onclick ='saveData();'>save layout</button>

<script type="module">
   import { statCard } from '../simplicity/themes/default/assets/js/components/stat.js';
   import { chartCard } from '../simplicity/themes/default/assets/js/components/chartCard.js';
   import { statCard2 } from '../simplicity/themes/default/assets/js/components/stat2.js';
   import { maintTable } from '../simplicity/themes/default/assets/js/components/maintenanceTable.js';
 /* $.post(JS.baseUrl+'api/getSlickSettings/dashboard', {}, function(data){
            if(data) {
                console.log(data);
                const items = data.widgets;
                var grid = GridStack.init();
                grid.load(items);
                //$('.welcome-screen').slideUp(1000, function(){ $(this).remove();});
                $('.welcome-screen').animate({ height: "12vh",}, 1500, 'linear', function(){ $(this).remove();});
                $('#greeting').slideUp(1000, function(){ $(this).remove();});

                
            }
        }, 'JSON');*/ 


              var items = [
            {w: 3, content: '<stat-card data-title="OUTSTANDING RENT" data-icon="icon-coins" data-source="dashboard/outstandingRent"></stat-card>'}, // will default to location (0,0) and 1x1
            {w: 3, content: '<stat-card data-title="UPCOMING VACANCIES" data-icon="icon-user" data-source="dashboard/upcomingVacancies"></stat-card>'}, // will be placed next at (1,0) and 2x1
            {w: 3, content: '<stat-card data-title="OPEN MANTENANCE TICKETS" data-icon="icon-tools" data-source="dashboard/openMaintenance" class="pink-icon"></stat-card>'},
            {w: 3, content: '<stat-card data-title="NEW BANK TRANSACTIONS" data-icon="icon-bank" data-source="dashboard/newBankTrans"></stat-card>'},
        
        
            {w: 5, h:4, content: '<chart-card data-title="Maintenace Tickets" data-chartType="pie" data-source="dashboard/transByMonth/3788"></chart-card>'},
            {w: 2, content: '<stat-card2 data-title="OUTSTANDING RENT" class="pink-background" data-source="dashboard/outstandingRent"></stat-card2>'},
            {w: 2, content: '<chart-card data-title="Rental Income By Month" data-chartType="bar" data-source="dashboard/transByMonth/3746"></chart-card>'},
            {w: 5, h:4, content: '<chart-card data-title="Repairs and maintenance by month" data-chartType="line" data-source="dashboard/transByMonth/3788"></chart-card>'},
            {w: 12, h:4, content: '<maint-table data-title="Maintenace Tickets"></maint-table>'}
          ];

          var grid = GridStack.init();
                grid.load(items);




  
</script>
<script>
function saveData() {
    var items2 = [];

    $('.grid-stack-item').each(function () {
        var $this = $(this);
        props = $($this).prop('gridstackNode');
        if(typeof props == undefined || props == undefined || !props.content) return;

        items2.push({
            y: $this.attr('gs-y'),
            x: $this.attr('gs-x'),
            w: $this.attr('gs-w'),
            h: $this.attr('gs-h'),
            content: props.content,
            id: $this.id
        });
    });
    //console.log(JSON.parse(items2));
    $.post('api/saveSlickSettings/dashboard', {widgets: items2});
    items2.forEach( function(item) {
        console.log(JSON.stringify(item));
    });
}




$('.grid-stack').on('change', function (e, items) {
  saveData();
});
</script>




