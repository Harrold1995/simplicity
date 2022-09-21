const textFormatter = function (row, cell, value, columnDef, dataContext) {
  if (typeof value === "number") {
    value = value.toString();
  }
  if (value == null || value == undefined || dataContext === undefined) {return "";}
  value = value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");

  const searchString = GlobalSearch.searchString;
  if (searchString) {
    const searchPosition = value.indexOf(searchString);
    if (searchPosition > -1) {
      value =
        value.substring(0, searchPosition) +
        `<span style="font-weight: 900; color:blue;">${searchString}</span>` +
        value.substring(searchPosition + searchString.length);
    }
  }
  return value;
};
const usdFormatter = function (row, cell, value, columnDef, dataContext) {
  if (typeof value === "number") {
    value = value.toString();
  }
  if (value == null || value == undefined || dataContext === undefined) {
    return "";
  }
  value = value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");

  const searchString = GlobalSearch.searchString;
  if (searchString) {
    const searchPosition = value.indexOf(searchString);
    if (searchPosition > -1) {
      value =
        value.substring(0, searchPosition) +
        `<span style="font-weight: 900; color:blue;">${searchString}</span>` +
        value.substring(searchPosition + searchString.length);
    } else {
      value = number_format(value);
    }
  } else {
    value = number_format(value);
  }
  return "$" + value;
};

function createGrid(rootElName, columns, onDblClick = () => {}) {
  var dataView;
  var grid;

  // initialize the model test
  dataView = new Slick.Data.DataView({ inlineFilters: true });
  dataView.beginUpdate();
  dataView.setItems([]);
  dataView.endUpdate();

  // initialize the grid
  var options = { forceFitColumns: true, rowHeight: 32, autoHeight: true };
  grid = new Slick.Grid(rootElName, dataView, columns, options);

  const groupItemMetadataProvider = new Slick.Data.GroupItemMetadataProvider(
    {},
    dataView
  );
  grid.registerPlugin(groupItemMetadataProvider);
  dataView.onRowsChanged.subscribe(function (e, args) {
    grid.invalidateRows(args.rows);
    grid.render();
    grid.resizeCanvas();
  });

  grid.onCellChange.subscribe(function (e, args) {
    dataView.updateItem(args.item.id, args.item);
  });

  dataView.onRowCountChanged.subscribe(function (e, args) {
    grid.updateRowCount();
    grid.render();
  });

  grid.onDblClick.subscribe((e, args) => {
    const item = dataView.getItem(args.row);
    onDblClick(item);
  });

  grid.onSort.subscribe(function (e, args) {
    const items = dataView.getItems();
    const field = args.sortCol.field;
    const sign = args.sortAsc ? 1 : -1;

    items.sort(function (dataRow1, dataRow2) {
      const value1 = dataRow1[field],
        value2 = dataRow2[field];
      return (value1 == value2 ? 0 : value1 > value2 ? 1 : -1) * sign;
    });

    dataView.setItems(items);
  });

  return dataView;
}

/* GlobalSearch */
GlobalSearch = {
  mode: "all", // 'all' | 'accounts' | 'transactions' | 'tenants'
  submode: "all",
  searchString: "",
  data: {},
  shownData: {},
  tables: {},
  init: () => {
    //accounts
    GlobalSearch.tables.accounts = createGrid(
      "#accountsSearchGroup .globalSearchRoot",
      [
        {
          id: "name",
          name: "Name",
          field: "name",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "description",
          name: "Description",
          field: "description",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "accno",
          name: "Account Number",
          field: "accno",
          sortable: true,
          formatter: textFormatter,
        },
      ],
      (item) => {
        JS.openDraggableModal("account", "edit", item.id);
      }
    );
    //transactions
    GlobalSearch.tables.transactions = createGrid(
      "#transactionsSearchGroup .globalSearchRoot",
      [
        {
          id: "transaction_date",
          name: "Transaction Date",
          field: "transaction_date",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "ref",
          name: "Reference",
          field: "ref",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "amount",
          name: "Amount",
          field: "amount",
          sortable: true,
          formatter: usdFormatter,
        },
        {
          id: "tname",
          name: "Name",
          field: "tname",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "account_name",
          name: "Account Name",
          field: "account_name",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "description",
          name: "Description",
          field: "description",
          sortable: true,
          formatter: textFormatter,
        },
        //{id: "terms", name: "Terms", field: "terms", sortable: true, formatter: textFormatter},
      ],
      (item) => {
        switch (item.transaction_type) {
          case "bill":
            return JS.openDraggableModal("2", "edit", item.transId);
          case "bill payment":
            return JS.openDraggableModal("bill payment", "edit", item.transId);
          case "charge":
            return JS.openDraggableModal("6", "edit", item.transId);
          case "check":
            return JS.openDraggableModal("4", "edit", item.transId);
          case "customer payments":
            return JS.openDraggableModal("5", "edit", item.transId);
          case "deposit":
            return JS.openDraggableModal("8", "edit", item.transId);
          case "journ":
            return JS.openDraggableModal("1", "edit", item.transId);
          case "Bank Transaction":
            return JS.openDraggableModal("15", "edit", item.transId);
          case "credit card":
            return JS.openDraggableModal("creditCard", "edit", item.transId);
          case "Invoice":
            return JS.openDraggableModal("18", "edit", item.transId);
        }
      }
    );
    //tenants
    GlobalSearch.tables.tenants = createGrid(
      "#tenantsSearchGroup .globalSearchRoot",
      [
        {
          id: "name",
          name: "Name",
          field: "name",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "email",
          name: "Email",
          field: "email",
          sortable: true,
          formatter: textFormatter,
        },
        {
          id: "phone",
          name: "Phone",
          field: "phone",
          sortable: true,
          formatter: textFormatter,
        },
      ],
      (item) => {
        JS.openDraggableModal("tenant", "edit", item.id);
      }
    );
  },

  changeMode: (type, subtype) => {
    GlobalSearch.mode = type;
    GlobalSearch.submode = subtype;
    GlobalSearch.render();
  },

  filterSearchResults: (type, container, items) => {
    if (!items || !items.length) {
      $(container).hide();
      return [];
    }

    if (GlobalSearch.mode === "all") {
      $(container).show();

      if (items.length > 5) {
        $(".globalSearchShowMore", container).show();
        $(".globalSearchShowMore", container).unbind("click");
        $(".globalSearchShowMore", container).bind("click", () => {
          GlobalSearch.changeMode(type);
        });
      } else {
        $(".globalSearchShowMore", container).hide();
      }
      return items.slice(0, 5);
    } else if (type === GlobalSearch.mode) {
      $(container).show();
      $(".globalSearchShowMore", container).hide();
      return items;
    } else {
      $(container).hide();
      return [];
    }
  },
  renderAccounts: (accounts) => {
    const showAccounts = GlobalSearch.filterSearchResults(
      "accounts",
      "#accountsSearchGroup",
      accounts
    );

    const accountsPreview = showAccounts.map((account) => {
      return {
        id: account.id,
        name: account.name,
        description: account.description,
        accno: account.accno,
      };
    });

    GlobalSearch.tables.accounts.setItems(accountsPreview);
  },

  renderTransactions: (items) => {
    let transactions = items || [];

    const container = "#transactionsSearchGroup";
    let showTransactions = [];

    const typeArr = transactions.map((item) => item.transaction_type);
    const uniqueTypes = typeArr.filter((transaction, index) => {
      return typeArr.indexOf(transaction) === index;
    });

    if (GlobalSearch.submode !== "all") {
      transactions = transactions.filter(
        (item) => item.transaction_type === GlobalSearch.submode
      );
    }

    if (!transactions || !transactions.length) {
      $(container).hide();
      showTransactions = [];
    } else if (GlobalSearch.mode === "all") {
      $(container).show();
      let shouldShowMore = false;

      uniqueTypes.forEach((type) => {
        const itemsWithThisType = transactions.filter(
          (item) => item.transaction_type === type
        );
        showTransactions = showTransactions.concat(
          itemsWithThisType.slice(0, 10)
        );
      });

      if (showTransactions.length > 5) {
        $(".globalSearchShowMore", container).show();
        $(".globalSearchShowMore", container).unbind("click");
        $(".globalSearchShowMore", container).bind("click", () => {
          GlobalSearch.changeMode("transactions");
        });
      } else {
        $(".globalSearchShowMore", container).hide();
      }
    } else if (GlobalSearch.mode === "transactions") {
      $(container).show();
      $(".globalSearchShowMore", container).hide();
      showTransactions = transactions;
    } else {
      $(container).hide();
      showTransactions = [];
    }

    const transactionsPreview = showTransactions.map((t) => {
      return {
        id: t.id,
        transId: t.trans_id,
        description: t.description,
        amount: parseFloat(t.amount),
        account_name: t.account_name,
        transaction_date: t.transaction_date,
        transaction_type: t.transaction_type,
        terms: t.terms,
        ref: t.ref,
        tname: t.tname,
      };
    });

    GlobalSearch.tables.transactions.setItems(transactionsPreview);

    GlobalSearch.tables.transactions.setGrouping({
      getter: "transaction_type",
      formatter: function (g) {
        const realCount = GlobalSearch.data.transactions.filter(
          (item) => item.transaction_type === g.value
        ).length;

        return (
          '<span style="font-size:14px;color:rgba(0,0,0,0.7);text-transform: uppercase;">' +
          "<b>" +
          g.value +
          "</b>" +
          "</span>&nbsp;&nbsp;" +
          `<span style="color:gray;font-size:14px;">${realCount} Items</span>`
        );
      },
      aggregateCollapsed: false,
      lazyTotalsCalculation: true,
    });
  },

  renderTenants: (tenants) => {
    const showTenants = GlobalSearch.filterSearchResults(
      "tenants",
      "#tenantsSearchGroup",
      tenants
    );

    const tenantsPreview = showTenants.map((tenant) => {
      return {
        id: tenant.id,
        name: `${tenant.first_name} ${tenant.last_name}`,
        email: tenant.email,
        phone: tenant.phone,
      };
    });

    GlobalSearch.tables.tenants.setItems(tenantsPreview);
  },

  search: (searchString) => {
    $.ajax({
      url: "search/search",
      method: "POST",
      data: { searchString },
      success: (response) => {
        GlobalSearch.data = {
          accounts: response.results.accounts,
          transactions: response.results.transactions,
          tenants: response.results.tenants,
        };

        GlobalSearch.searchString = searchString;
        GlobalSearch.changeMode("all", "all");
        GlobalSearch.render();
      },
    });
  },

  render: () => {
    const data = GlobalSearch.data;

    // render menu
    GlobalSearch.renderMenu(data);
    $(".lds-roller").hide();

    // clean all items to avoid resizing bug
    GlobalSearch.renderAccounts(null);
    GlobalSearch.renderTransactions(null);
    GlobalSearch.renderTenants(null);

    GlobalSearch.renderAccounts(data.accounts);
    GlobalSearch.renderTransactions(data.transactions);
    GlobalSearch.renderTenants(data.tenants);

    $(".globalSearchResultsWrapper")
      .off("click")
      .on("click", ".slick-group", function () {
        $(".slick-group-toggle", this).click();
      });
  },

  renderMenu: (data) => {
    $(".globalSearchMenu").empty();

    const accounts = data.accounts || [];
    const transactions = data.transactions || [];
    const tenants = data.tenants || [];

    $(
      ".globalSearchCount",
      ".globalSearchMenuItem[data-type='all'][data-subtype='all']"
    ).text(accounts.length + transactions.length + tenants.length);

    if (accounts.length) {
      $(".globalSearchMenu").append(`
        <div class="globalSearchMenuItem" data-type="accounts" data-subtype="all">
          Accounts <span class="globalSearchCount">${accounts.length}</span>
        </div>
      `);
    }

    if (transactions.length) {
      $(".globalSearchMenu").append(`
        <div class="globalSearchMenuItem" data-type="transactions" data-subtype="all">
          Transactions <span class="globalSearchCount">${transactions.length}</span>
        </div>
      `);

      const typeArr = transactions.map((item) => item.transaction_type);
      const uniqueTypes = typeArr.filter((transaction, index) => {
        return typeArr.indexOf(transaction) === index;
      });

      // create menu items
      uniqueTypes.forEach((type) => {
        const itemsWithThisType = transactions.filter(
          (item) => item.transaction_type === type
        ).length;
        $(".globalSearchMenu").append(`
          <div class="globalSearchMenuItem" data-type="transactions" data-subtype="${type}" style="padding-left:20px">
            ${type} <span class="globalSearchCount">${itemsWithThisType}</span>
          </div>
        `);
      });
    }

    if (tenants.length) {
      $(".globalSearchMenu").append(`
        <div class="globalSearchMenuItem" data-type="tenants" data-subtype="all">
          Tenants <span class="globalSearchCount">${tenants.length}</span>
        </div>
      `);
    }

    $(".globalSearchMenuItem").removeClass("selected");
    $(
      `.globalSearchMenuItem[data-type='${GlobalSearch.mode}'][data-subtype='${GlobalSearch.submode}']`
    ).addClass("selected");

    // Menu items
    $(".globalSearchMenuItem")
      .off("click")
      .on("click", function () {
        const type = $(this).data("type");
        const subtype = $(this).data("subtype");
        GlobalSearch.changeMode(type, subtype);
      });
  },
};

/* UI */
$(document).ready(function () {
  $("#headerSearchButton").click(() => {
    let position = $("#headerSearchButton").position();
    $("#headerSearchButton").hide();
    $("#headerSearchWrapper").css("display", "flex");
    $("#headerSearchInput").focus();
    $("#search-container-wrapper")
      .show()
      .css({
        "z-index": JS.maxZindex++,
        top: position.top + 60,
        left: position.left - 400,
        position: "absolute",
      });
  });

  $("#closeHeaderSearchBtn").click(() => {
    $("#headerSearchButton").show();
    $("#headerSearchWrapper").hide();
  });

  //detailed search
  $("#headerSearchForm").submit((event) => {
    event.preventDefault();
    const inputText = $("#headerSearchInput").val();
    JS.openDraggableModal("custom", "search", null, null, {
      url: "search/getModal",
      searchString: inputText,
    });
    $("#search-container-wrapper").hide();
  });

  $("#advancedSearch").click((event) => {
    event.preventDefault();
    const inputText = $("#headerSearchInput").val();
    JS.openDraggableModal("custom", "search", null, null, {
      url: "search/getModal",
      searchString: inputText,
    });
  });

  //instant search
  const searchCont = $("#search-container")[0];
  $("#headerSearchForm").focus((event) => {
    event.preventDefault();
    const inputText = $("#headerSearchInput").val();
    const type = $("#headerSearchInput").attr('data-type');
    instantSearch(inputText,type);
  });

  $("#headerSearchInput").focus((event) => {
    getInstantSearchData();
    $("#search-container-wrapper").show();
  });


  $(".searchTabs").click((event) => {
    event.preventDefault();
    type = $(event.target).attr("data-type");
    const inputText = $("#headerSearchInput").val();
    $("#headerSearchInput").attr('data-type',type);
    instantSearch(inputText, type);
    $(".searchTabs").each(function() {
        $( this ).closest('li').removeClass('active');
    });
    $(event.target).closest('li').addClass('active');

  });
  
  $("#headerSearchForm").keyup((event) => {
    event.preventDefault();
    const inputText = $("#headerSearchInput").val();
    type = $("#headerSearchInput").attr('data-type');
    instantSearch(inputText,type);
  });

  //fetching new searchdata from remote db to local db so that
  //a new request isnt initiated for ever character in the search box
  const getInstantSearchData = async () => {
    //fetch from db
    res = await fetch("api/getInstantSearchData");
    res2 = await res.json();
    const types1 = Object.values(res2);

    //opening local db
    var request = indexedDB.open("simpl", 1);

    request.onerror = (event) => {
      console.error(`Database error: ${event.target.errorCode}`);
    };

    //if the db doesnt exist yet...
    request.onupgradeneeded = (event) => {
      let db = event.target.result;
      // create the instant search object store (table)
      db.createObjectStore("InstantSearch", {
        autoIncrement: true,
      });
    };

    request.onsuccess = (event) => {
      let db = event.target.result;
      const txn = db.transaction("InstantSearch", "readwrite");

      // get the object store
      const store = txn.objectStore("InstantSearch");
      //
      //let query = store.put(types1);
      let query = store.put(types1, 1);

      query.onsuccess = function (event) {};
         console.log('overwritten!');
      // handle the error case
      query.onerror = function (event) {
        console.log(event.target.errorCode);
      };

      // close the database once the
      // transaction completes
      txn.oncomplete = function () {
        db.close();
      };
    };
  };

  const instantSearch = async (inputText, searchtype) => {
    $(searchCont).empty();
    let types = [];
    var request = indexedDB.open("simpl", 1);
    request.onerror = (event) => {
      console.error(`Database error: ${event.target.errorCode}`);
    };

    request.onsuccess = (event) => {
      let db = event.target.result;
      var transaction = db.transaction(["InstantSearch"]);
      var objectStore = transaction.objectStore("InstantSearch");
      var request = objectStore.get(1);
      request.onerror = function (event) {
        // Handle errors!
        console.log(error);
      };
      request.onsuccess = function (event) {
        types = request.result;
        types.forEach((type, index) => {
          if(  Object.keys(type).length > 0 && (searchtype=='all' || type[0].type==searchtype)){

            let matches = type.filter((res3) => {
              const regex = new RegExp(
                `${inputText.replaceAll(" ", "(.*?)\\s")}`,
                "gi"
              );
              return res3.searchText.match(regex) || res3.id.match(regex);
            });
            if (matches.length > 0) {
              outputHtml(matches);
            }

          } 
            

          
        });
      };
    };
  };

  const outputHtml = (matches) => {
    switch (matches[0].type) {
      case "tenants":
        html = matches
          .map(
            (match) => `
          <div class = "instant-search-container" data-id='${match.id}' data-pid='${match.pid}' data-type='tenant' data-lid='${match.lid}'>
            <div class = "instant-search-icon-container">
            <i class="icon-user"></i>
            </div>
            <div class = "instant-search-content-container">
              <div class = "instant-search-content-header">
                <a id='searchName' data-id='${match.id}' data-pid='${match.pid}' data-type='tenant' data-lid='${match.lid}'>${match.name } </a><span>Tenant</span>
              </div>
              <div class = "instant-searsch-content-content">
                ${match.propname} unit ${match.unitname}<br>
                <i class="icon-phone" aria-hidden="true"></i> <small>${
                  match.phone ? match.phone : "------------"
                }</small>
                <i class="icon-envelope" aria-hidden="true"></i> <a class ='email'>${
                  match.email
                }</a>
                
              </div>
              <div class = "instant-search-content-actions">
              <a data-id='${match.id}' data-type='${
              match.type
            }'  id = "editModal" onclick="event.stopPropagation(); JS.openDraggableModal('tenant', 'edit', ${
              match.id
            });" >Edit Tenant</a> - <a onclick="event.stopPropagation(); JS.openDraggableModal('lease', 'edit', ${
              match.lid
            });" >Edit Lease</a> - 
            <a onclick="event.stopPropagation(); JS.receive_payment(${match.id},${match.pid},${match.unit_id},${match.lid})"> Recieve Payment</a>
             -
             <a onclick="event.stopPropagation(); JS.newCharge(${match.id},${match.lid})"> Add Charge</a>
            </div>
          </div>

          </div>
          `
          )
          .join("");
        break;
      case "vendors":
        html = matches
          .map(
            (match) => `
          <div class = "instant-search-container" data-id='${match.id}' data-type='vendors'>
            <div class = "instant-search-icon-container">
            <i class="icon-user"></i>
            </div>
            <div class = "instant-search-content-container">
              <div class = "instant-search-content-header">
                <a id='searchName' data-id='${match.id}' data-pid='null' data-type='${match.type}' data-lid='${match.lid}'>${match.name} </a><span>Vendor</span>
              </div>
              <div class = "instant-search-content-content">
              <i class="icon-phone" aria-hidden="true"></i> <small>${
                match.phone ? match.phone : "------------"
              }</small>
              <i class="icon-envelope" aria-hidden="true"></i> <a class ='email'>${
                match.email ? match.email : "-@----.com"
              }</a><br>
              <small>${
                match.address ? match.address : ""
              }</small>
              
            </div>
              <div class = "instant-search-content-actions">
                <a data-id='${match.id}' data-type='${match.type}'  id = "editModal" onclick="event.stopPropagation(); JS.openDraggableModal('${match.type}', 'edit', ${match.id});" >Edit</a> -
                
                <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('4', 'add', null, null, {profile: ${match.id}});" >Write A check</a> - 
                <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('2', 'add', null, null, {profile: ${match.id}});" >Enter A Bill</a>
            </div>
          </div>

          </div>
          `
          )
          .join("");
        break;
      case "properties":

        html = matches
          .map(
            (match) => `
            <div class = "instant-search-container" data-id='${match.id}' data-pid='null' data-type='property'>
            <div class = "instant-search-icon-container">
            <i class="icon-city"></i>
            </div>
            <div class = "instant-search-content-container">
              <div class = "instant-search-content-header">
                <a id='searchName' data-id='${match.id}' data-pid='null' data-type='property'>${match.name}</a><span>Property</span>
              </div>
              <div class = "instant-search-content-content">
              <span>${match.def_account}</span></br>
              <span>${match.entity}</span>
              
              </div>
              <div class = "instant-search-content-actions">
                <a data-id='${match.id}' data-type='${match.type}' onclick="event.stopPropagation(); JS.openDraggableModal('property', 'edit', ${match.id});" >Edit</a> -
                
                <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('4', 'add', null, null, {property: ${match.id});" >Write A check</a> - 
                <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('2', 'add', null, null, {property: ${match.id}});" >Enter A Bill</a> - 
                <a href="#" class="reportLink" data-id="5" title="" defaults="${match.id}$$01/01/2022|01/26/2022">Profit &amp; Loss</a> -
                <a href="#" class="reportLink" data-id="57" title="" defaults="${match.id}$$01/26/2022">Balance Sheet</a> - 
                <a href="#" class="reportLink"  data-id="7" title="" defaults="${match.id}$$01/26/2022">Rent Roll</a>
            </div>
          </div>

          </div>
          `)
          .join("");

        break;
      case "accounts":
        let newdate = new Date();

        html = matches
        .map(
          (match) => `
            <div class = "instant-search-container" data-id='${match.id}' data-pid='null' data-type='account' data-lid='${match.lid}'>
              <div class = "instant-search-icon-container">
              <i class="icon-coins"></i>
              </div>
              <div class = "instant-search-content-container">
                <div class = "instant-search-content-header">
                  <a id='searchName' data-id='${match.id}' data-pid='null' data-type='${match.type}' data-lid='${match.lid}'>${match.name} </a><span>Account</span>
                </div>
                <div class = "instant-search-content-content">
                <span>${match.account_type ? match.account_type : ""}</span>
                <small>(${match.account_category ? match.account_category : ""})</small>
              </div>
                <div class = "instant-search-content-actions">
                  <a data-id='${match.id}' data-type='${match.type}'  id = "editModal" onclick="event.stopPropagation(); JS.openDraggableModal('account', 'edit', ${match.id});" >Edit</a> -
                  
                  <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('4', 'add', null, null, {account: ${match.id}});" >Write A check</a> -
                  <a href="#" class="reportLink"  data-id="17" title="" defaults="${match.id}$$${`${newdate.getMonth()+1}/${newdate.getDate()}/${newdate.getFullYear()}`}">Quickreport</a>
              </div>
            </div>

            </div>
          `
          )
          .join("");
        break;
      case "owners":
          html = matches
          .map(
            (match) => `
          <div class = "instant-search-container  data-id='${match.id}' data-pid='null' data-type='${match.type}' data-lid='${match.lid}'">
            <div class = "instant-search-icon-container">
            <i class="icon-user"></i>
            </div>
            <div class = "instant-search-content-container">
              <div class = "instant-search-content-header">
                <a id='searchName' data-id='${match.id}' data-pid='null' data-type='${match.type}' data-lid='${match.lid}'>${match.name} </a><span>Owner</span>
              </div>
              <div class = "instant-search-content-content">
              <i class="icon-phone" aria-hidden="true"></i> <small>${
                match.phone ? match.phone : "------------"
              }</small>
              <i class="icon-envelope" aria-hidden="true"></i> <a class ='email'>${
                match.email ? match.email : "-@----.com"
              }</a><br>
              <small>${
                match.address ? match.address : ""
              }</small>
              
            </div>
              <div class = "instant-search-content-actions">
                <a data-id='${match.id}' data-type='${match.type}'  id = "editModal" onclick="event.stopPropagation(); JS.openDraggableModal('investor', 'edit', ${match.id});" >Edit</a> -
                
                <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('4', 'add', null, null, {profile: ${match.id}});" >Write A check</a> - 
                <a data-id='${match.id}' data-type='${match.type}'  onclick="event.stopPropagation(); JS.openDraggableModal('2', 'add', null, null, {profile: ${match.id}});" >Enter A Bill</a>
            </div>
          </div>

          </div>
        `
        )
        .join("");
        break;
      case "task":
        break;
      case "report":
        break;
    }

    $(searchCont).append(html);
  };
});
