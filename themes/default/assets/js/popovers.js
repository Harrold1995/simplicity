
const INITIAL_CONTENT = `<nav class="nav-a d-nav popover-nav">
<h2><a href="./">Menu</a></h2>
<ul>
    <li class="active"><a href="http://[::1]/Custom_styles/simplicity/documents/0/"><i class="icon-home" aria-hidden="true"></i> Home</a></li>
    <li><a href="http://[::1]/Custom_styles/simplicity/documents/favorites/"><i class="icon-star" aria-hidden="true"></i> Favorites</a></li>
    <li><a href="http://[::1]/Custom_styles/simplicity/documents/paths/"><i class="icon-star" aria-hidden="true"></i> Saved Paths</a></li>
    <li><a href="http://[::1]/Custom_styles/simplicity/documents/unsorted/"><i class="icon-time" aria-hidden="true"></i> Unsorted</a></li>
    <li><a href="./"><i class="icon-trash" aria-hidden="true"></i> Recycling Bin</a></li>
</ul>
<ul>
    <li><a href="./"><i class="icon-certificate" aria-hidden="true"></i> Bills</a></li>
    <li><a href="./"><i class="icon-certificate" aria-hidden="true"></i> Statements</a></li>
    <li><a href="./"><i class="icon-receipt" aria-hidden="true"></i> Receipts</a></li>
    <!--<li><a href="./"><i class="icon-certificate"></i> Income</a></li>-->
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Leases</a></li>
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Contracts</a></li>
</ul>
<ul>
    <li><a href="./"><i class="icon-home" aria-hidden="true"></i> Properties</a></li>
    <li><a href="./"><i class="icon-user" aria-hidden="true"></i> People</a></li>
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Transactions</a></li>
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Accounts</a></li>
    <li><a href="./"><i class="icon-trash2" aria-hidden="true"></i> Units</a></li>
</ul>
<ul>
    <li class="active"><a href="http://[::1]/Custom_styles/simplicity/documents/0/"><i class="icon-home" aria-hidden="true"></i> Home</a></li>
    <li><a href="http://[::1]/Custom_styles/simplicity/documents/favorites/"><i class="icon-star" aria-hidden="true"></i> Favorites</a></li>
    <li><a href="http://[::1]/Custom_styles/simplicity/documents/paths/"><i class="icon-star" aria-hidden="true"></i> Saved Paths</a></li>
    <li><a href="http://[::1]/Custom_styles/simplicity/documents/unsorted/"><i class="icon-time" aria-hidden="true"></i> Unsorted</a></li>
    <li><a href="./"><i class="icon-trash" aria-hidden="true"></i> Recycling Bin</a></li>
</ul>
<ul>
    <li><a href="./"><i class="icon-certificate" aria-hidden="true"></i> Bills</a></li>
    <li><a href="./"><i class="icon-certificate" aria-hidden="true"></i> Statements</a></li>
    <li><a href="./"><i class="icon-receipt" aria-hidden="true"></i> Receipts</a></li>
    <!--<li><a href="./"><i class="icon-certificate"></i> Income</a></li>-->
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Leases</a></li>
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Contracts</a></li>
</ul>
<ul>
    <li><a href="./"><i class="icon-home" aria-hidden="true"></i> Properties</a></li>
    <li><a href="./"><i class="icon-user" aria-hidden="true"></i> People</a></li>
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Transactions</a></li>
    <li><a href="./"><i class="icon-bill" aria-hidden="true"></i> Accounts</a></li>
    <li><a href="./"><i class="icon-trash2" aria-hidden="true"></i> Units</a></li>
</ul>
</nav>`

const state = {
  isFetching: false,
  canFetch: true
}

tippy('#ajax-tippy', {
  content: INITIAL_CONTENT,
  interactive: true,
  theme: 'light',
  delay: 100,
  arrow: true,
  arrowType: 'round',
  size: 'large',
  duration: 500,
  animation: 'scale',
  async onShow(tip) {
    if (state.isFetching || !state.canFetch) return

    state.isFetching = true
    state.canFetch = false

    try {
      const response = await fetch('https://unsplash.it/200/?random')
      const blob = await response.blob()
      const url = URL.createObjectURL(blob)
      if (tip.state.isVisible) {
        const img = new Image()
        img.width = 200
        img.height = 200
        img.src = url
        //tip.setContent(img)
      }
    } catch (e) {
      tip.setContent(`Fetch failed. ${e}`)
    } finally {
      state.isFetching = false
    }
  },
  onHidden(tip) {
    state.canFetch = true
    tip.setContent(INITIAL_CONTENT)
  }
})
