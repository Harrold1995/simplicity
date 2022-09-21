<div class="modal" id="addFavoriteModal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Save current path</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <label for="foldername">Folder name:</label>
                <input id="foldername" type="text" placeholder="Type folder name here..." />
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <!--<button type="button" data-dismiss="modal" class="btn btn-danger">Close</button>-->
                <button type="button" class="btn btn-primary" id="submitFavoritesButton">Add to favorites</button>
                <button type="button" class="btn btn-primary" id="submitPathsButton">Save the path</button>
            </div>
        </div>
    </div>
</div>