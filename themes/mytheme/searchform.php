<form class="search-form" mathod="GET" action="<?php echo esc_url(site_url('/')); ?>">
            <label class="headline headline--medium" for="s">Perform a new Search</label>
            <div class="search-form-row">
                <input class="s" id = "s" type ="search" name="s" placeholder="What are you looking for?">
                <input class="search-submit" type="submit" value="Search">
            </div>
        </form>