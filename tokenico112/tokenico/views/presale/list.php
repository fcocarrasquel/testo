<div class="container presale-container">
    <div class="filter-disable-el"></div>
    <form class="filter">
        <?php echo esc_html__('Status: ', 'tokenico'); ?>
        <select id="status">
            <option value="all"><?php echo esc_html__('All', 'tokenico'); ?></option>
            <option value="started"><?php echo esc_html__('Started', 'tokenico'); ?></option>
            <option value="not-started"><?php echo esc_html__('Not started', 'tokenico'); ?></option>
            <option value="ended"><?php echo esc_html__('Ended', 'tokenico'); ?></option>
        </select>
        <?php echo esc_html__('Network: ', 'tokenico'); ?>
        <select id="network">
            <option value="all"><?php echo esc_html__('All', 'tokenico'); ?></option>
            <?php foreach ($chains as $hexId => $chain) : ?>
                <option value="<?php echo esc_attr($hexId) ?>"><?php echo esc_html($chain['name']); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="t-button filter-button" data-text="<?php echo esc_attr__('Please wait...', 'tokenico'); ?>">
            <?php echo esc_html__('Filter', 'tokenico'); ?>
        </button>
    </form>
    <div class="presale-row row justify-content-center presales-content">
        <?php $this->viewEcho('presale/item', compact('presales')); ?>
    </div>
    <?php if ($presales->max_num_pages > 1) : ?>
        <div class="load-more-button-wrapper">
            <button class="load-more-button t-button" data-text="<?php echo esc_attr__('Loading...', 'tokenico'); ?>" data-max-page="<?php echo esc_attr($presales->max_num_pages); ?>" data-page="1">
                <?php echo esc_html__('Load more presale', 'tokenico'); ?>
            </button>
        </div>
    <?php endif; ?>
</div>