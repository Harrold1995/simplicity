<div class="modal fade" id="addBillModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="vertical-alignment-helper">
    <div class="modal-dialog vertical-align-center modal-lg" role="document">
      <div class="modal-content">
        <form action="properties/addProperty" method="post">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">New Bill</h5>

            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container-fluid">
              <div class="row">
                <div class="col">
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" checked>
                    <label class="custom-control-label" for="customRadioInline1">Bill</label>
                  </div>
                  <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input">
                    <label class="custom-control-label" for="customRadioInline2">Credit</label>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col-5">
                  <div class="form-group">
                    <label for="property">Property</label>
                    <select class="form-control" id="property">
                      <option>123 Main Street</option>
                      <option>1600 Pennsylvania Ave</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="vendor">Vendor</label>
                    <select class="form-control" id="vendor">
                      <option>ABC Contracting, INC.</option>
                      <option>DEF Contracting, INC.</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="class">Class</label>
                    <select class="form-control" id="class">
                      <option>Construction</option>
                      <option>Marketing</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="terms">Terms</label>
                    <select class="form-control" id="terms">
                      <option>30 Days</option>
                      <option>60 Days</option>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label for="reference">Reference</label>
                    <input type="text" class="form-control" id="reference" placeholder="1234">

                  </div>
                  <div class="form-group">
                    <label for="bill-date">Bill Date</label>
                    <input type="text" class="form-control" id="bill-date" placeholder="4/1/2018">
                  </div>
                  <div class="form-group">
                    <label for="bill-due">Bill Due</label>
                    <input type="text" class="form-control" id="bill-due" placeholder="5/1/2018">
                  </div>
                  <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="text" class="form-control" id="amount" aria-describedby="amountHelp" placeholder="$100,000.00">
                    <small id="amountHelp" class="form-text text-muted">Total amount for this bill.</small>
                  </div>
                </div>
                <div class="col-3 mt-4">
                  <button type="button" class="btn btn-primary btn-block">
                    Attach original
                  </button>
                  <div class="form-group mt-4">
                    <label for="approval">Request approval from</label>
                    <select class="form-control" id="approval">
                      <option>None</option>
                      <option>General Manager</option>
                    </select>
                  </div>
                  <div class="form-group mt-5">
                    <label for="memo">Memo</label>
                    <textarea class="form-control" id="memo" rows="3"></textarea>
                  </div>
                </div>
              </div>

              <div class="row mt-3">
                <div class="col">
                  <table class="table table-b">
                    <thead class="thead-light">
                      <tr>
                        <th scope="col">Account</th>
                        <th scope="col">Property</th>
                        <th scope="col">Unit</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Name</th>
                        <th scope="col">Class</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                          <select class="form-control" id="account">
                            <option>Repair & Maintenance</option>
                            <option>Advertising</option>
                          </select></td>
                        <td>
                          <select class="form-control" id="property">
                            <option>123 Main Street</option>
                            <option>1600 Pennsylvania Ave</option>
                          </select></td>
                        <td>
                          <select class="form-control" id="unit">
                            <option>2A</option>
                            <option>2B</option>
                          </select></td>
                        <td><textarea class="form-control" id="description" rows="1"></textarea></td>
                        <td>
                          <input type="text" class="form-control" id="amount" placeholder="$100.00" style="max-width: 110px">
                        </td>
                        <td>
                          <select class="form-control" id="name">
                            <option>John Doe</option>
                            <option>Jane Doe</option>
                          </select></td>
                        </td>
                        <td>
                          <select class="form-control" id="Class">
                            <option>Construction</option>
                            <option>Marketing</option>
                          </select></td>
                        </td>
                      </tr>
                      <td>
                        <select class="form-control" id="account">
                          <option>Repair & Maintenance</option>
                          <option>Advertising</option>
                        </select></td>
                      <td>
                        <select class="form-control" id="property">
                          <option>123 Main Street</option>
                          <option>1600 Pennsylvania Ave</option>
                        </select></td>
                      <td>
                        <select class="form-control" id="unit">
                          <option>2A</option>
                          <option>2B</option>
                        </select></td>
                      <td><textarea class="form-control" id="description" rows="1"></textarea></td>
                      <td>
                        <input type="text" class="form-control" id="amount" placeholder="$100.00" style="max-width: 110px">
                      </td>
                      <td>
                        <select class="form-control" id="name">
                          <option>John Doe</option>
                          <option>Jane Doe</option>
                        </select></td>
                      </td>
                      <td>
                        <select class="form-control" id="Class">
                          <option>Construction</option>
                          <option>Marketing</option>
                        </select></td>
                      </td>
                      </tr>
                      <td>
                        <select class="form-control" id="account">
                          <option>Repair & Maintenance</option>
                          <option>Advertising</option>
                        </select></td>
                      <td>
                        <select class="form-control" id="property">
                          <option>123 Main Street</option>
                          <option>1600 Pennsylvania Ave</option>
                        </select></td>
                      <td>
                        <select class="form-control" id="unit">
                          <option>2A</option>
                          <option>2B</option>
                        </select></td>
                      <td><textarea class="form-control" id="description" rows="1"></textarea></td>
                      <td>
                        <input type="text" class="form-control" id="amount" placeholder="$100.00" style="max-width: 110px">
                      </td>
                      <td>
                        <select class="form-control" id="name">
                          <option>John Doe</option>
                          <option>Jane Doe</option>
                        </select></td>
                      </td>
                      <td>
                        <select class="form-control" id="Class">
                          <option>Construction</option>
                          <option>Marketing</option>
                        </select></td>
                      </td>
                      </tr>

                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
              Save & New
            </button>
            <button type="button" class="btn btn-primary">
              Save & Close
            </button>
            <button type="button" class="btn btn-primary">
              Duplicate
            </button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--<form  class="popup-a form-entry modal fade" id="addBillModal" data-title="new-bill-1" id="draggable">
<header>
<h2 class="text-uppercase">New bill</h2>
<ul class="check-a">
<li><label for="feg"><input type="radio" id="feg" name="feg" checked> Bill</label></li>
<li><label for="feh"><input type="radio" id="feh" name="feg"> Credit</label></li>
</ul>
<nav>
<ul>
<li><a href="./"><i class="icon-chevron-left"></i> <span>Previous</span></a></li>
<li><a href="./"><i class="icon-chevron-right"></i> <span>Next</span></a></li>
<li><a href="./"><i class="icon-trash"></i> <span>Delete</span></a></li>
<li><a href="./"><i class="icon-envelope-outline"></i> <span>Envelope</span></a></li>
<li><a href="./"><i class="icon-brain"></i> <span>Brain</span></a></li>
<li><a href="./"><i class="icon-documents"></i> <span>Copy</span></a></li>
<li><a href="./"><i class="icon-paperclip"></i> <span>Attach</span></a></li>
<li><a class="print" href="./"><i class="icon-print"></i> <span>Print</span></a></li>
</ul>
</nav>
</header>
<section class="a">
<div class="double d m20">
<div>
<p>
<label for="fei">Property</label>
<select id="fei" name="fei">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</p>
<p>
<label for="fej">Vendor</label>
<select id="fej" name="fej">
<option>ABC Contracting</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</p>
<p class="w325">
<label for="fek">Class</label>
<select id="fek" name="fek">
<option>Construction</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</p>
<p class="w325">
<label for="fel">Terms</label>
<select id="fel" name="fel">
<option>30 days</option>
<option>40 days</option>
<option>40% deposit</option>
<option>15 days</option>
<option>10 days</option>
</select>
</p>
</div>
<div>
<p>
<label for="fem">Reference</label>
<input type="text" id="fem" name="fem" value="4101">
</p>
<p>
<label for="fen">Bill Date</label>
<input type="text" id="fen" name="fen" value="12/31/2017" class="date">
</p>
<p>
<label for="feo">Amount <span class="prefix">$</span></label>
<input type="text" id="feo" name="feo" value="100,000.02">
</p>
<p>
<label for="fep">Due Date</label>
<input type="text" id="fep" name="fep" value="1/31/2017" class="date">
</p>
</div>
</div>
<p>
<label for="feq">Memo:</label>
<input type="text" id="feq" name="feq" value="CitiCard">
</p>
<p class="submit">
<span>
<label for="fer">Request approval from</label>
<select id="fer" name="fer">
<option>none</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</span>
<button type="submit">Attach original</button>
</p>
</section>
<table class="table-c">
<thead>
<tr>
<th>Account</th>
<th>Property</th>
<th>Unit</th>
<th>Description</th>
<th class="text-center">Amount</th>
<th>Name</th>
<th>Class</th>
</tr>
</thead>
<tbody>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>this should be editable div (not input field) with max width so it expands vertically with more text</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
<tr>
<td>
<select class="w135">
<option>Repairs &amp; Main</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w115">
<option>123 Main St</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w55">
<option>2-C</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>for blah blah bla</td>
<td class="text-center"><span class="text-left">$</span> 102.23</td>
<td>
<select class="w110">
<option>Some Name</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
<td>
<select class="w100">
<option>Some Class</option>
<option>Position #1</option>
<option>Position #2</option>
<option>Position #3</option>
<option>Position #4</option>
<option>Position #5</option>
</select>
</td>
</tr>
</tbody>
<tfoot>
<tr>
<td></td>
<td></td>
<td></td>
<td class="text-right">Total:</td>
<td class="text-center"><span class="text-left">$</span> 1.200,00</td>
<td></td>
<td></td>
</tr>
</tfoot>
</table>
<footer>
<ul class="list-btn">
<li><button type="submit">Save &amp; New</button></li>
<li><a href="./">Duplicate</a></li>
<li><button type="reset">Cancel</button></li>
<li><a href="./">Save &amp; Close</a></li>
</ul>
<ul>
<li>Last Modified 12:22:31 pm 1/10/2018</li>
<li>Last Modified by <a href="./">User</a></li>
</ul>
</footer>
</form>-->

<script>
    $(document).ready(function () {
        JS.initDraggableModal('#addBillButton', '#addBillModal')
    });
</script>
