<header>
    <nav class="nav-a d-nav">
        <h2>Menu</h2>
        <ul>
            <li><a href="<?php echo base_url('documents/0/');?>"><i class="icon-home"></i> Home</a></li>
            <li><a href="<?php echo base_url('documents/favorites/');?>"><i class="icon-star"></i> Favorites</a></li>
            <li><a href="<?php echo base_url('documents/paths/');?>"><i class="icon-star"></i> Saved Paths</a></li>
            <li><a href="<?php echo base_url('documents/unsorted/');?>"><i class="icon-time"></i> Unsorted</a></li>
            <li><a href="./"><i class="icon-trash"></i> Recycling Bin</a></li>
        </ul>
        <ul>
            <li><a href="./"><i class="icon-certificate"></i> Bills</a></li>
            <li><a href="./"><i class="icon-certificate"></i> Statements</a></li>
            <li><a href="./"><i class="icon-receipt"></i> Receipts</a></li>
            <!--<li><a href="./"><i class="icon-certificate"></i> Income</a></li>-->
            <li><a href="./"><i class="icon-bill"></i> Leases</a></li>
            <li><a href="./"><i class="icon-bill"></i> Contracts</a></li>
        </ul>
        <ul>
            <li><a href="./"><i class="icon-home"></i> Properties</a></li>
            <li><a href="./"><i class="icon-user"></i> People</a></li>
            <li><a href="./"><i class="icon-bill"></i> Transactions</a></li>
            <li><a href="./"><i class="icon-bill"></i> Accounts</a></li>
            <li><a href="./"><i class="icon-trash2"></i> Units</a></li>
        </ul>
    </nav>
</header>
<article id="documents-wrapper">
    <div id="documents-header">
        <div class="breadcrumbs d-breadcrumbs">
        </div>
        <header class="d-flex justify-content-between">
            <div class="float-left d-flex">
                <div class="d-filters-overlay"></div>
                <div class="d-filters-back float-left">
                    <i class="fas fa-arrow-circle-left"></i>
                </div>
                <div class="d-filters-button float-left">
                    <button type="button"></button>
                    <div class="d-filters-popup"></div>
                </div>
            </div>
            <div class="form-search double d-flex">
                <ul class="list-square">
                    <li class="a mr-2 mb-0" style="display:none;"><a href="#" id="addFavoritesButton"><i class="icon-plus"></i> <span>Add</span></a></li>
                </ul>
                <p>
                    <label for="fsa">Search</label>
                    <input type="text" id="psearch" name="fsa" class="wide" required>
                    <button type="submit">Submit</button>
                    <a href="./"><i class="icon-microphone"></i> <span>Record</span></a>
                </p>
            </div>
        </header>
        <ul class="list-view d-none">
            <li><a href="./"><i class="icon-grid"></i> <span>Grid</span></a></li>
            <li><a href="./"><i class="icon-list"></i> <span>List</span></a></li>
        </ul>
    </div>
    <div id="documents-container"></div>
</article>
<footer>
    <form id="filesForm" action="./" method="post">
        <p class="input-file a"><label for="d-files"><input type="file" id="d-files" name="d-files" multiple> Drop files here</label></p>
    </form>
</footer>

<script>
    $(document).ready(function () {
        var documents = new Documents('#documents-wrapper',
            {
                base: '<?php echo base_url();?>',
                docsurl: '<?php echo base_url();?>documents/',
                path: '<?php echo $path;?>',
                values: '<?php echo $values; ?>',
                headerId: '#documents-header',
                bodyId: '#documents-container',
                footerId: '#documents-footer',
                filtersLabelId: '.d-filters-button button',
                filtersPopupId: '.d-filters-popup',
                filtersOverlayId: '.d-filters-overlay',
                tableAjaxUrl: 'documents/getAjaxTable',
                folderIcon: 'https://cdn2.iconfinder.com/data/icons/ourea-icons/256/Folder_-_Open_256x256-32.png',
                pathDelimiter: '/',
                favoritesButton: '#addFavoritesButton',
                favoritesModal: '#addFavoriteModal',
                favoritesSubmitButton : '#submitFavoritesButton',
                pathsSubmitButton : '#submitPathsButton',
                favoritesModalUrl: 'documents/getAddFavoritesModal',
                favoritesSubmitUrl: 'documents/addFavoritesFolder',
                pathsSubmitUrl: 'documents/savePaths',
                fileTemplateUrl: 'documents/getFileTemplate',
                filesUploadUrl: 'documents/uploadFiles',
                fileSelectUrl: 'documents/getFileSelect',
                filesDeleteUrl: 'documents/deleteFiles',
                docsDeleteUrl: 'documents/deleteDocs',
                uploadTemplateUrl: 'documents/getUploadTemplate',
                pathArray: ["unsorted", "favorites", "paths"]
            });
    });
</script>