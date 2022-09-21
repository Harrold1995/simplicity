<div class="modal fade property-modal hide" id="SearchModal" tabindex="-1" role="dialog" type="search" ref-id="" aria-hidden="true" style="margin-left: 100px">
    <div class="modal-dialog modal-dialog-centered modal-xl " role="document">
		<div id="root">
      <div class="modal-content text-primary popup-a form-entry shown" style="padding: 25px">
        <!-- Search input -->
        <header class="modal-h">

          <div class="globalSearchWrapper" id="modalSearchWrapper">
            <div id="globalSearchInputWrapper">
              <form class="globalSearchForm" id="modalSearchForm">
                <span></span>
                <input class="globalSearchInput" id="modalSearchInput" value="<?= $search_string ?>" placeholder="Search for tenants, buildings, etc..">
              </form>
            </div>
            
          </div>	
          <div style = 'width: 50%;'>
             <a href="#" id="globalSearchCloseBtn" data-dismiss="modal" aria-label="close"><i class="icon-x-thin"></i></a>
          </div>
	
				</header>
        

        <!-- Results -->
        <div class="globalSearchResultsWrapper">
          <div class="globalSearchMenuWrapper">
              <div class="globalSearchMenuItem" data-type="all" data-subtype="all">All <span class="globalSearchCount">0</span></div>
              <div class="globalSearchMenu"></div>
              <!-- 
              <div class="globalSearchMenuItem" data-type="accounts" data-subtype="all">Accounts <span class="globalSearchCount">0</span></div>
              <div class="globalSearchMenuItem" data-type="transactions" data-subtype="all">Transactions <span class="globalSearchCount">0</span></div>
              <div class="globalSearchTransactionsMenu"></div>
              <div class="globalSearchMenuItem" data-type="tenants" data-subtype="all">Tenants <span class="globalSearchCount">0</span></div>
              -->
          </div>
          <div class="globalSearchResultsItems">
          <div class="lds-roller" style ="padding-top: 150px;  overflow:hidden;  padding-left: 500px; min-width:600px;  min-height:600px"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
            <div class="globalSearchResultsGroup" id="accountsSearchGroup">
              <h2>Accounts</h2>
              <div class="globalSearchRoot"></div>
              <div class="globalSearchShowMore">Show more</div>
            </div>
            <div class="globalSearchResultsGroup" id="transactionsSearchGroup">
              <h2>Transactions</h2>
              <div class="globalSearchRoot"></div>
              <div class="globalSearchShowMore">Show more</div>
            </div>
            <div class="globalSearchResultsGroup" id="tenantsSearchGroup">
              <h2>Tenants</h2>
              <div class="globalSearchRoot"></div>
              <div class="globalSearchShowMore">Show more</div>
            </div>
          </div>
        </div>
      </div>
	  </div>
  </div>
</div>

<script>
  GlobalSearch.init();
  GlobalSearch.search("<?= $search_string ?>");

  $('#modalSearchForm').submit((e) => {
    e.preventDefault();
    const searchString = $('#modalSearchInput').val();
    GlobalSearch.search(searchString);
  })
</script>