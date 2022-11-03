<div class="container">
    <div class="row justify-content-center">
        <div class="presale-col col-md-4 presale-col-content">
            <div class="presale-content">
                <div class="title">
                    <h3 class="white-title">
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
                            <?php echo esc_html__('Name: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo esc_html($token->name); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Symbol: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo esc_html($token->symbol); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Address: ', 'tokenico'); ?>
                        </div>
                        <div class="value hide-text">
                            <?php echo esc_html($token->address); ?>
                        </div>
                    </div>
                    <div class="info">
                        <div class="title">
                            <?php echo esc_html__('Total supply: ', 'tokenico'); ?>
                        </div>
                        <div class="value">
                            <?php echo esc_html($token->totalSupply); ?>
                        </div>
                    </div>
                    --------------------------------
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
                    <br>
                    <div>
                        <?php echo esc_html__('Times are in UTC time zone!', 'tokenico'); ?>
                    </div>
                    <div class="copy-token-address t-button" data-address="<?php echo esc_attr($token->address); ?>">
                        <?php echo esc_html__('Copy token address', 'tokenico'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($status == 'started' || $status == 'ended' && !$presale->autoTransfer) : ?>
    <div class="row justify-content-center">
        <div id="tokenico-presale"><?php echo esc_html__('Loading...', 'tokenico'); ?></div>
    </div>
<?php endif; ?>