define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function (
        $,
        modal
    ) {

        function initializeModalComponent() {
            var options = {
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: 'Popup title',
                buttons: [{
                    text: $.mage.__('Close'),
                    class: '',
                    click: function () {
                        this.closeModal();
                    }
                }]
            };

            var popup = modal(options, $('.banner-modal'));
            $(".banner-wrapper").click(function () {
                $('.banner-modal').modal('openModal');
            });

            return popup;
        }

        function createModal() {
            let wrapper = $('<div class="banner-wrapper"></div>');
            let bannerContent = $('<div class="banner-content"></div>');
            let bannerModal = $('<div class="banner-modal"></div>');
            let bannerModalPopup = $('<div class="banner-modal-popup"></div>');
            let bannerModalPopupContent = $('<div class="banner-modal-popup_content"></div>');

            bannerModalPopup.append(bannerModalPopupContent);
            bannerModal.append(bannerModalPopup);

            wrapper.append(bannerContent);
            wrapper.append(bannerModal);
            // return $(`
            //     <div class="banner-wrapper">
            //         <div class="banner-content">
            //
            //         </div>
            //         <div class="banner-modal">
            //             <div class="banner-modal-popup">
            //                 <div class="banner-modal-popup_content">
            //
            //                 </div>
            //             </div>
            //         </div>
            //     </div>
            // `);
            return wrapper;
        }

        function getViewedBannersId() {
            let storage = JSON.parse(localStorage.getItem('viewedBanners'));
            return storage !== null ? storage : [];
        }

        function setViewedBannersId(id) {
            let persistedBanners = getViewedBannersId();
            persistedBanners.push(id);
            localStorage.setItem('viewedBanners', JSON.stringify(persistedBanners));
        }

        function getBannerToDisplay(banners) {
            let rand = Math.floor(Math.random() * banners.length);
            let currentBanner = banners[rand];
            if (currentBanner.show_once === '1') setViewedBannersId(currentBanner.banner_id);
            return banners[rand];
        }

        function setModalData(data) {
            $(".banner-modal .banner-modal-popup_content").append($(data.banner_popup_text_content));
            $(".banner-wrapper .banner-content").append($(data.banner_text_content));
        }

        return function (options) {
            let URL = options.baseUrl;
            let showedBanners = getViewedBannersId();

            $.ajax({
                url: URL + 'banner/banner?getBanner=true&viewedBanners=' + showedBanners.join(),
                method: 'GET',
                context: $('.sections.nav-sections'),
                dataType: 'JSON',
                success: function (banners) {
                    let currentBanner = getBannerToDisplay(banners);
                    if (currentBanner) {
                        let modal = createModal();
                        $(this).append(modal);
                        initializeModalComponent();
                        setModalData(currentBanner);
                    }
                },
                error: function (e) {
                    // ....
                }
            });
        }
    }
);
