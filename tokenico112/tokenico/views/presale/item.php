<?php if ($presales->have_posts()) : ?>
    <?php foreach($presales->posts as $presale) : 
        $presale = $this->presaleInstance($presale->ID);
        $token = json_decode($presale->token);
        $status = $presale->getStatus();
        ?>
        <div class="presale-col col-md-4">
            <div class="presale-item">
                <div class="preview-image">
                    <?php echo get_the_post_thumbnail($presale->ID); ?>
                </div>
                <div class="title">
                    <h3>
                        <?php echo get_the_title($presale->ID); ?>
                    </h3>
                </div>
                <div class="start-status">
                    <?php if ($status == 'started') {
                        echo esc_html__('Presale started', 'tokenico');    
                    } elseif ($status == 'ended') {
                        echo esc_html__('Presale ended', 'tokenico');   
                    } else {
                        echo esc_html__('Presale not started', 'tokenico'); 
                    } ?>
                </div>
                <div class="infos">
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Network: ', 'tokenico'); ?>
                        </div>
                        <div class="value hide-text">
                            <?php echo esc_html($presale->getNetworkName()); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Total sale limit: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getTotalSaleLimit(); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Remaining limit: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getRemainingLimit(); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Min contribution: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getMinContribution(); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Max contribution: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getMaxContribution(); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Exchange rate: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getExchangeRate(); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Start date: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getStartDate(); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('End date: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo $presale->getEndDate(); ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <a href="<?php echo esc_url(get_the_permalink($presale->ID)); ?>" class="t-button review-btn">
                    <?php echo esc_html__('Review', 'tokenico'); ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="not-found">
        <?php echo esc_html__('Not found presale!', 'tokenico'); ?>
    </div>
<?php endif; ?>