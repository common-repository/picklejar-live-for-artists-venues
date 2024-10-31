let ACC = {
    global: {
        _autoload: [],
        pluginName: 'picklejar-live-for-artists-venues',
        on: function(element, event, fn) {
            for (let a = 0; a < element.length; a++) element[a].addEventListener(event, fn);
        }
    }
};

ACC.Artist = {
    _autoload: [],
    requestSliderId: null,
    eventsList: {},
    defaultFilters: {
        handle: '',
        exclude_ngo_artists: 1,
        exclude_music_pro_artists: 0,
        exclude_users: 1,
        stage_name: '',
        exclude_business: 1,
        page: 0
    },
    artistModel: data => ({
        id: data.id,
        artistType: data.artistType,
        categories: data.categories,
        name: data.stageName || data.handle,
        headline: data.headline,
        handle: data.handle,
        message: data.bio,
        description: data.bio,
        website: data.website,
        hometownCity: data.hometownCity,
        hometownState: data.hometownState,
        hometownCountry: data.hometownCountry,
        isVerified: data.isVerified,
        isRecommended: data.isRecommended,
        avatar: data.avatar,
        genre: data.genreDetails,
        banner: data.banner,
        tipButtonId: data.tipButtonId,
        tipButton: data.tipButton,
        tipsEnabled: data.tipsEnabled,
        publicLink: data.publicLink,
        qrLink: data.qrLink,
        socialLinks: data.socialLinks,
        completedChallenges: data.completedChallenges,
        qrCodeBannerId: data.qrCodeBannerId,
        qrCodeBanner: data.qrCodeBanner,
        forwardTipsToDefaultEventId: data.forwardTipsToDefaultEventId,
        ngo: data.ngo,
        musicProId: data.musicProId,
        hospitalityId: data.hospitalityId,
        personalityId: data.personalityId,
        broadcasterId: data.broadcasterId,
        isFollowing: data.isFollowing
    }),
    getArtists: (target, pageSize, filters) => {
        if (target) ACC.Utils.getDataset(target, pageSize, filters); else {
            const eventsList = document.querySelectorAll('.picklejar-load-events .picklejar-entities-row');
            if (eventsList.length) for (let eventElement of eventsList) ACC.Utils.getDataset(eventElement, pageSize, filters);
        }
    },
    queryArtists: (currentElement, elementPageSize, elementFilters, elementStyles) => {
        const formattedResponse = {};
        return ACC.Fetch.disableActions(currentElement), ACC.Artist.fetchArtists(elementPageSize, elementFilters).then(rawData => (formattedResponse.currentPage = rawData.headers.get('X-Pagination-Current-Page'), 
        formattedResponse.totalPages = rawData.headers.get('X-Pagination-Page-Count'), rawData.json())).then(response => {
            try {
                let {success: success, data: data} = response;
                if (!0 === success) if (data.length) {
                    ACC.Utils.updateDataFilters(currentElement, formattedResponse, elementFilters);
                    const eventParametersStyles = elementStyles;
                    let eventContent = '';
                    if ('slider' === eventParametersStyles.layout) {
                        eventContent += ACC.Render.renderSliderTemplate(data, eventParametersStyles);
                        const swiperSlider = ACC.Swiper.sliders[ACC.Events.requestSliderId];
                        ACC.InfiniteScroll.loadMore && swiperSlider ? (swiperSlider.appendSlide(eventContent), 
                        swiperSlider.allowSlideNext = !0, swiperSlider.allowTouchMove = !0, swiperSlider.slideNext()) : (currentElement.innerHTML = eventContent, 
                        ACC.Swiper.initPicklejarSlider(null)), ACC.Events.requestSliderId = null;
                    } else eventContent += ACC.Render.renderGridTemplate(data, eventParametersStyles), 
                    ACC.InfiniteScroll.loadMore ? currentElement.innerHTML += eventContent : currentElement.innerHTML = eventContent;
                } else currentElement.innerHTML = ACC.Messages.noArtistsFoundMessage; else ACC.Dashboard.showErrorNotification(response.error, 'alert');
            } catch (error) {
                console.log('error', error), ACC.Dashboard.showErrorNotification(error, 'alert');
            }
        }).catch(error => {
            console.log('error', error), ACC.Dashboard.showErrorNotification(error, 'alert');
        }).finally(() => {
            ACC.Fetch.enableActions(currentElement), ACC.InfiniteScroll.infiniteScrollLoader && (ACC.InfiniteScroll.enableInfiniteScroll = !0, 
            ACC.InfiniteScroll.loadMore = !1);
        });
    },
    fetchArtists: async (pageSize, filters) => {
        const {action: action, nonce: nonce, url: url, entityFilter: entityFilter, renderTemplate: renderTemplate} = pj_artist_list;
        ACC.EntityDetails.nonce = nonce;
        const defaultFilters = {
            action: action,
            nonce: nonce,
            page_size: pageSize || 12
        };
        return Object.assign(defaultFilters, ACC.Artist.defaultFilters), ACC.Dashboard.entityDetailsPage = renderTemplate, 
        ACC.Fetch.fetchEntities(url, defaultFilters, entityFilter, filters);
    }
}, ACC.Dashboard = {
    _autoload: [ [ 'bindShowImageUpload', document.querySelectorAll('.js-image-upload').length ], [ 'bindShowLoading', document.querySelectorAll('.pj-loading-button').length ], [ 'clearActivation', document.querySelectorAll('.picklejar-bg-danger').length ], [ 'removeOnLoad', document.querySelectorAll('.picklejar-remove-on-load').length ], [ 'bindTinyMCE', document.querySelectorAll('.wp-editor-container').length ], [ 'bindTabs', document.querySelectorAll('[data-pj-tab]').length ], [ 'bindToggleTarget', document.querySelectorAll('[data-picklejar-toggle-target]').length ] ],
    sortableClass: '.js-sortable',
    imgRemoveClass: 'js-img-remove',
    galleryWrapperClass: 'gallery-wrapper',
    defaultImage: 'https://pbs.twimg.com/profile_images/1365349444795965449/DdjGmOeG_400x400.jpg',
    coords: {
        accuracy: null,
        latitude: null,
        longitude: null
    },
    entityDetailsPage: null,
    randomId: () => Math.floor(400 * Math.random()),
    bindShowLoading: () => {
        const loadingButtons = document.querySelectorAll('.pj-loading-button');
        for (let loadingButton of loadingButtons) loadingButton.addEventListener('click', () => {
            ACC.Fetch.triggerButton = loadingButton, loadingButton.dataset && loadingButton.dataset.target && (ACC.Fetch.triggerButtonTarget = loadingButton.dataset.target);
        });
    },
    bindShowImageUpload: () => {
        let customUploader;
        const $imgUpload = document.querySelectorAll('.js-image-upload');
        let $galleryWrapper = jQuery('.' + ACC.Dashboard.galleryWrapperClass);
        $imgUpload.forEach(elem => {
            const $imgUploadContainer = jQuery(elem).closest('.js-img-upload-container'), $input = $imgUploadContainer.find('input'), isMultiple = !!$input.attr('multiple');
            if (isMultiple) console.log(isMultiple); else {
                const $removeBtn = $imgUploadContainer.find('.js-img-remove');
                $input.val().trim().length ? $removeBtn.show() : $removeBtn.hide();
            }
        }), ACC.Dashboard.getImgRemove($galleryWrapper), document.querySelectorAll('.js-image-upload').forEach(imageElement => {
            imageElement.addEventListener('click', triggerImage => {
                triggerImage.preventDefault();
                const $this = jQuery(triggerImage.target), multiple = $this.attr('multiple');
                customUploader = wp.media({
                    title: 'Select or upload an  image',
                    library: {
                        type: 'image'
                    },
                    button: {
                        text: 'Select Image'
                    },
                    multiple: multiple
                }).on('select', () => {
                    jQuery($this).siblings('input')[0].value = customUploader.state().get('selection').map(galleryWrapper => {
                        multiple || jQuery($galleryWrapper).find('li').remove();
                        const removeImgBtn = '<button type="button" class="picklejar-btn picklejar-btn-remove remove ' + ACC.Dashboard.imgRemoveClass + ' remove remove-img" id="' + galleryWrapper.toJSON().id + '">&times;</button>';
                        return jQuery($galleryWrapper).append('<li class="picklejar-form-control img-item js-img-item"><div class="default-img img-thumbnail picklejar-img-wrapper" style="width: ' + $galleryWrapper.data('width') + 'px; height: ' + $galleryWrapper.data('height') + 'px">' + removeImgBtn + '<img src="' + galleryWrapper.toJSON().url + '"></div></li>'), 
                        ACC.Dashboard.getImgRemove($galleryWrapper), jQuery('.picklejar-container').css('backgroundImage', 'url("' + galleryWrapper.toJSON().url + '")'), 
                        galleryWrapper.toJSON().id;
                    }).join();
                }), $galleryWrapper = $this.siblings('.gallery-wrapper'), customUploader.open();
            });
        });
    },
    getImgRemove: $galleryWrapper => {
        const $imgRemove = document.querySelectorAll('.' + ACC.Dashboard.imgRemoveClass), $imgWrapper = jQuery($galleryWrapper).find('li .img-thumbnail.img-wrapper');
        $imgRemove.length && $imgRemove.forEach(imgRemover => {
            imgRemover.addEventListener('click', () => {
                let $this = jQuery(imgRemover), imgID = $this.prop('id'), $input = $this.closest('.js-img-upload-container').find('input');
                imgID && ($input.each((index, $inputField) => {
                    if ($inputField.value.length) {
                        let imgArray = $input.val().split(',');
                        $input.val(ACC.Dashboard.removeItem(imgArray, imgID));
                    }
                    $imgWrapper.length > 1 ? $this.closest('.js-img-item').remove() : ($galleryWrapper = jQuery($galleryWrapper)).find('.img-thumbnail').html('<h3>' + $galleryWrapper.data('width') + ' x ' + $galleryWrapper.data('height') + '</h3>');
                }), jQuery('.picklejar-container').css('backgroundImage', ''));
            });
        });
    },
    removeItem: (array, elem) => array.filter(e => e !== elem),
    showErrorNotification: (message, type = 'notification') => {
        ACC.Dashboard.showNotifications(message, 'error', type);
    },
    showWarningNotification: (message, type = 'notification') => {
        ACC.Dashboard.showNotifications(message, 'warning', type);
    },
    showSuccessNotification: (message, type = 'notification') => {
        ACC.Dashboard.showNotifications(message, 'success', type);
    },
    clearNotifications: notificationContainer => {
        notificationContainer.innerHTML = '';
    },
    showNotifications: (messages, style, type = 'notification') => {
        const isDashboardPage = document.querySelectorAll('.wp-admin');
        let showMessage = messages;
        const signal = ACC.Fetch.signal;
        if (showMessage && 'The user aborted a request.' === showMessage.message || signal && signal.aborted && 'Canceling Previous Call' === signal.reason) return !1;
        if (isDashboardPage.length) {
            switch (style) {
              case 'error':
                messages.message && (showMessage = messages.message), messages.fieldErrors && Object.values(messages.fieldErrors).forEach(errorMessage => {
                    errorMessage.message ? showMessage += `<p>${errorMessage.message}</p>` : showMessage += errorMessage;
                });
                break;

              case 'success':
              case 'warning':
                messages.message && (showMessage = messages.message);
            }
            if ('notification' === type) {
                document.querySelectorAll('.picklejar-notification').forEach(notificationContainer => {
                    notificationContainer.innerHTML = `<div class="notice notice-${style}"><span class="picklejar-messaje">${showMessage}</span><button class="btn picklejar-close-notification">&times</button></div>`, 
                    document.querySelectorAll('.picklejar-close-notification').forEach(closeNotificationButton => {
                        closeNotificationButton.addEventListener('click', () => ACC.Dashboard.clearNotifications(notificationContainer));
                    });
                });
            }
            if ('alert' === type) {
                let $pjNotification = document.querySelectorAll('.picklejar-notification-alert');
                if (!$pjNotification.length) {
                    const alertHTML = document.createElement('div');
                    alertHTML.classList.add('picklejar-notification', 'picklejar-notification-alert'), 
                    document.querySelector('body').append(alertHTML), $pjNotification = document.querySelectorAll('.picklejar-notification-alert');
                }
                $pjNotification.forEach(notificationContainer => {
                    notificationContainer.innerHTML = `<div class="notice notice-${style} picklejar-alert"><span class="picklejar-messaje">${showMessage}</span><button class="btn picklejar-close-notification">&times</button></div>`, 
                    document.querySelectorAll('.picklejar-close-notification').forEach(closeNotificationButton => {
                        closeNotificationButton.addEventListener('click', () => ACC.Dashboard.clearNotifications(notificationContainer));
                    });
                });
                const itemRows = document.querySelectorAll('.picklejar-item-row');
                if (itemRows.length) for (const itemRow of itemRows) itemRow.innerHTML = `<div class="notice notice-error">${showMessage}</div>`;
            }
        } else ACC.Dashboard.showErrorPage();
    },
    clearActivation: () => {
        document.querySelectorAll('.picklejar-bg-danger').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('picklejar_integration_plugin[settings][pj_validation_token]').value = '', 
                document.getElementById('picklejar_integration_plugin[settings][pj_entity_type]').value = '';
                const artistManager = document.getElementById('picklejar_artist_layout_manager'), eventsManager = document.getElementById('picklejar_events_layout_manager');
                artistManager && (artistManager.checked = !1), eventsManager && (eventsManager.checked = !1), 
                ACC.Dashboard.removeCookie('pj_validation_token'), document.getElementById('submit').click();
            });
        });
    },
    serializeForm: (form, action, nonce) => {
        const formData = new FormData(form);
        let data = {
            action: action,
            nonce: nonce
        };
        for (let field of formData.entries()) data[field[0]] = field[1];
        return data;
    },
    getCookie: cookieName => {
        const name = cookieName + '=', ca = decodeURIComponent(document.cookie).split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            for (;' ' === c.charAt(0); ) c = c.substring(1);
            if (0 === c.indexOf(name)) return c.substring(name.length, c.length);
        }
        return '';
    },
    removeCookie: cookieName => {
        document.cookie = cookieName + '=; Path=/; max-age=0;';
    },
    showErrorPage: () => {
        const {action: action, nonce: nonce, url: url} = pj_error_page, data = {
            action: action,
            nonce: nonce
        };
        ACC.Fetch.getData(url, data).then(response => response.json()).then(result => {
            const picklejarContainers = document.querySelectorAll('.picklejar-container');
            for (const picklejarContainer of picklejarContainers) picklejarContainer.innerHTML = result.content, 
            picklejarContainer.setAttribute('style', '');
        });
    },
    removeOnLoad: () => {
        const removeOnLoadList = document.querySelectorAll('.picklejar-remove-on-load');
        for (const removeOnLoad of removeOnLoadList) removeOnLoad.remove();
    },
    bindTinyMCE: () => {
        setTimeout(() => {
            const editorContainerList = document.querySelectorAll('.wp-editor-container');
            for (const editorContainer of editorContainerList) {
                const iframe = editorContainer.querySelector('iframe'), targetInput = editorContainer.querySelector('.wp-editor-area');
                if (iframe && targetInput) {
                    const alignItems = document.querySelectorAll('.mce-menu-item'), body = iframe.contentWindow.document.querySelector('body');
                    for (const alignButton of alignItems) alignButton.addEventListener('click', () => {
                        ACC.Dashboard.sanitizeTinyMCEValue(body.innerHTML, targetInput);
                    });
                    body.addEventListener('keyup', e => {
                        ACC.Dashboard.sanitizeTinyMCEValue(e.target.innerHTML, targetInput);
                    });
                }
            }
        }, 3e3);
    },
    sanitizeTinyMCEValue: (value, targetInput) => {
        value = (value = (value = value.replaceAll('<p>', '<div>')).replaceAll('<p', '<div')).replaceAll('</p>', '</div>'), 
        targetInput.value = value, targetInput.dispatchEvent(new Event('keyup'));
    },
    getLocation: () => {
        navigator.geolocation && navigator.geolocation.getCurrentPosition(position => {
            ACC.Dashboard.coords = position.coords;
        }, error => {
            console.log('An error occurred... The error code and message are: ' + error.code + '/' + error.message);
        });
    },
    toggleClass: (targetElements, type, className, parentElement) => {
        const targetsElementsNode = parentElement.querySelectorAll(targetElements);
        if (targetsElementsNode.length) for (const targetElement of targetsElementsNode) {
            const nodeElement = targetElement.classList;
            'add' === type ? nodeElement.add(className) : nodeElement.remove(className);
        }
    },
    toggleDisable: (targetElements, disabled) => {
        const targetsElementsNode = document.querySelectorAll(targetElements);
        if (targetsElementsNode.length) for (const targetElement of targetsElementsNode) targetElement.readOnly = disabled;
    },
    bindTabs: () => {
        const tabsContainerList = document.querySelectorAll('[data-pj-tab]'), toggleActiveTabs = (currentElement, type) => {
            const targetElements = currentElement.getAttribute('data-pj-tab'), parentElement = currentElement.closest('.picklejar-tabs-container');
            ACC.Dashboard.toggleClass(targetElements, type, 'picklejar-show', parentElement);
        }, change = element => {
            (targetElement => {
                const menuElements = targetElement.closest('.picklejar-tabs-container').querySelector('.picklejar-nav').querySelectorAll('[data-pj-tab]');
                for (const menuElement of menuElements) menuElement.classList.remove('picklejar-active'), 
                toggleActiveTabs(menuElement, 'remove');
            })(element.target), element.target.classList.add('picklejar-active'), toggleActiveTabs(element.currentTarget, 'add');
        };
        for (const tabsContainer of tabsContainerList) tabsContainer.addEventListener('click', change, !1);
    },
    bindToggleTarget: () => {
        const togglerList = document.querySelectorAll('[data-picklejar-toggle-target]'), changeToggleEvent = element => {
            const currentElement = element.currentTarget, dataAttributeTarget = currentElement.getAttribute('data-picklejar-toggle-target');
            ACC.Dashboard.toggleDisable(dataAttributeTarget, !currentElement.checked);
        };
        if (togglerList.length) for (const toggle of togglerList) toggle.addEventListener('change', changeToggleEvent, !1);
    }
}, ACC.Debounce = {
    _autoload: [],
    debounceTimeoutId: null,
    debounceTimeout: 500,
    debounceFunction: (callback, wait) => {
        window.clearTimeout(ACC.Dashboard.debounceTimeoutId), ACC.Dashboard.debounceTimeoutId = window.setTimeout(() => {
            callback();
        }, wait);
    }
}, ACC.Elementor = {
    _autoload: [ [ 'isElementorEditor', document.querySelectorAll('#elementor-preview').length ] ],
    isElementorEditor: () => {
        setTimeout(() => {
            document.querySelectorAll('.picklejar-container').length && ACC.Fetch.getEntities(null, null, null);
        }, 3e3);
    }
}, ACC.EntityDetails = {
    _autoload: [ [ 'bindLoadImages', document.querySelectorAll('.picklejar-entity-details-template').length ], [ 'triggerArtistActions', document.querySelectorAll('.picklejar-artist-details-template').length ], [ 'getEventDetails', document.querySelectorAll('.picklejar-event-details-template').length ] ],
    fetchData: !1,
    entityType: null,
    nonce: null,
    bindLoadImages: () => {
        const hiddenClass = document.querySelector('.picklejar-entity-details-template .picklejar-hidden');
        hiddenClass && (hiddenClass.classList.remove('picklejar-hidden'), document.querySelector('.picklejar-loader').remove());
    },
    triggerArtistActions: () => {
        const id = new URLSearchParams(document.location.search).get('entityId');
        switch (!0) {
          case 'Artists' === ACC.EntityDetails.entityType:
            ACC.EntityDetails.getArtistDetails(id);
            break;

          case 'Venues' === ACC.EntityDetails.entityType:
            ACC.EntityDetails.getVenuesDetails(id);
            break;

          default:
            ACC.EntityDetails.getArtistDetails(id);
        }
    },
    getArtistDetails: artistId => {
        const {action: action, nonce: nonce, url: url, entityId: entityId} = pj_artist_details, id = artistId || entityId, data = {
            action: action,
            nonce: nonce,
            id: id
        };
        document.querySelectorAll('#picklejar-upcoming').length && ACC.EntityDetails.getUpcomingData(id), 
        document.querySelectorAll('#picklejar-listen').length && ACC.EntityDetails.getMediaData(id), 
        ACC.Fetch.getData(url, data).then(response => response.json()).then(result => {
            const {data: data, success: success} = result;
            try {
                if (!0 === success) {
                    const artistData = ACC.Artist.artistModel(data);
                    ACC.EntityDetails.renderEntityDetails(artistData);
                } else ACC.Dashboard.showErrorNotification(result.error, 'alert');
            } catch (err) {
                ACC.Dashboard.showErrorNotification(err, 'alert');
            }
        }).catch(error => {
            alert('3'), ACC.Dashboard.showErrorNotification(error, 'alert'), console.log('error', error);
        }).finally(() => {
            ACC.EntityDetails.fetchData = !0;
        });
    },
    getEventDetails: () => {
        const id = new URLSearchParams(document.location.search).get('entityId');
        if (id) {
            const {action: action, nonce: nonce, url: url} = pj_event_details, data = {
                action: action,
                nonce: nonce,
                id: id
            };
            ACC.Fetch.getData(url, data).then(response => response.json()).then(result => {
                const {data: data, success: success} = result;
                try {
                    if (!0 === success) {
                        const eventData = ACC.Events.eventsModel(data);
                        ACC.EntityDetails.renderEntityDetails(eventData);
                    } else ACC.Dashboard.showErrorNotification(result.error, 'alert');
                } catch (err) {
                    ACC.Dashboard.showErrorNotification(err, 'alert');
                }
            }).catch(error => {
                ACC.Dashboard.showErrorNotification(error, 'alert'), console.log('error', error);
            }).finally(() => {
                ACC.EntityDetails.fetchData = !0;
            });
        }
    },
    getVenuesDetails: venueId => {
        const {action: action, nonce: nonce, url: url, entityId: entityId} = pj_venues_details, id = venueId || entityId, data = {
            action: action,
            nonce: nonce,
            id: id
        };
        document.querySelectorAll('#picklejar-upcoming').length && ACC.EntityDetails.getUpcomingData(id), 
        document.querySelectorAll('#picklejar-listen').length && ACC.EntityDetails.getMediaData(id), 
        ACC.Fetch.getData(url, data).then(response => response.json()).then(result => {
            const {data: data, success: success} = result;
            try {
                if (!0 === success) {
                    const artistData = ACC.Artist.artistModel(data);
                    ACC.EntityDetails.renderEntityDetails(artistData);
                } else ACC.Dashboard.showErrorNotification(result.error, 'alert');
            } catch (err) {
                ACC.Dashboard.showErrorNotification(err, 'alert');
            }
        }).catch(error => {
            alert('3'), ACC.Dashboard.showErrorNotification(error, 'alert'), console.log('error', error);
        }).finally(() => {
            ACC.EntityDetails.fetchData = !0;
        });
    },
    getTipButtonsDetails: () => {
        const {action: action, nonce: nonce, url: url} = pj_search_tip_button, data = {
            action: action,
            nonce: nonce
        };
        return ACC.Fetch.getData(url, data);
    },
    renderEntityDetails: data => {
        if (document.querySelectorAll('.picklejar-entity-details-template').length) {
            const eventDates = data.eventDates ? data.eventDates[0] : null, startDate = eventDates ? eventDates.formattedDateTimeStart : null, content = {
                '.picklejar-entity-details-image': `<img src="${data.banner.largeImage || ACC.Dashboard.defaultImage}" alt="none">`,
                '.picklejar-avatar': `<img class="picklejar-avatar-image" src="${data.avatar.originalImage}" alt="avatar-image">`,
                '.picklejar-title': data.name + (data.handle ? `<small class="picklejar-handle">${data.handle}</small>` : ''),
                '.picklejar-event-details-description': data.description
            };
            if (startDate && (content['.picklejar-events-details-start-date'] = `<p><span class="material-symbols-outlined">event</span> <span class="text">${startDate.month} ${startDate.date}, ${startDate.year}</span></p>\n                 <p><span class="material-symbols-outlined">schedule</span> <span class="text">${startDate.time}</span></p>\n                 <p><span class="material-symbols-outlined">location_on</span> <span class="text">${data.address.formatted_address}</span><p>`, 
            'event' === data.eventType && (content['.picklejar-events-details-start-date'] += `<p><span class="material-symbols-outlined">confirmation_number</span><span class="text">${startDate.day}</span><p>`)), 
            data.socialLinks && data.socialLinks.length) {
                content['.picklejar-social-links'] = '';
                for (const socialLink of data.socialLinks) {
                    const socialNetworkName = socialLink.social.name;
                    document.getElementById(`picklejar-${socialNetworkName.toLowerCase()}`).setAttribute('href', socialLink.userSocialLink);
                }
            }
            if (data.qrLink && (content['#picklejar-qrLink'] = `<img class="picklejar-qrcode" src="${data.qrLink}" alt="qr-code">`), 
            data.tipButton && data.tipsEnabled) content['.picklejar-event-details-tip'] = `<img class="picklejar-event-details-tip" \n            onmouseover="this.src='../wp-content/plugins/${ACC.global.pluginName}/assets/images/tip-coming-soon.png'"\n            style="opacity: .5" src="${data.tipButton.originalImage}" alt-src="../wp-content/plugins/${ACC.global.pluginName}/assets/images/tip-coming-soon.png">`, 
            ACC.EntityDetails.updateHTMLContent(content); else if (data.tipButtonId) {
                const buttonId = data.tipButtonId;
                ACC.EntityDetails.getTipButtonsDetails().then(response => response.json()).then(result => {
                    const {data: data, success: success} = result;
                    try {
                        if (!0 === success) {
                            const matchButton = data.filter(buttonData => buttonData.id === buttonId);
                            if (matchButton.length) {
                                const tipButton = matchButton[0];
                                content['.picklejar-event-details-tip'] = `<img class="picklejar-event-details-tip"\n                        onmouseover="this.src='../wp-content/plugins/${ACC.global.pluginName}/assets/images/tip-coming-soon.png'"\n                        style="opacity: .5" src="${tipButton.media.originalImage}" alt-src="../wp-content/plugins/${ACC.global.pluginName}/assets/images/tip-coming-soon.png">`, 
                                ACC.EntityDetails.updateHTMLContent(content);
                            }
                        } else ACC.Dashboard.showErrorNotification(result.error, 'alert');
                    } catch (err) {
                        ACC.Dashboard.showErrorNotification(err, 'alert');
                    }
                }).catch(error => {
                    ACC.Dashboard.showErrorNotification(error, 'alert'), console.log('error', error);
                }).finally(() => {
                    ACC.EntityDetails.fetchData = !0;
                });
            } else content['.picklejar-event-details-tip'] = `<img alt="tip button" class="picklejar-event-details-tip" style="opacity: .5" src="../wp-content/plugins/${ACC.global.pluginName}/assets/images/tip-coming-soon.png">`;
        }
    },
    updateHTMLContent: content => {
        for (let targetElement of Object.keys(content)) {
            const eventDetailsContent = document.querySelectorAll(targetElement);
            eventDetailsContent.length && (eventDetailsContent[0].innerHTML = content[targetElement]);
        }
    },
    getUpcomingData: id => {
        const {action: action, nonce: nonce, url: url, renderTemplate: renderTemplate} = pj_upcoming;
        Object.assign(ACC.Events.defaultFilters, {
            action: action,
            nonce: nonce,
            start_date: ACC.Utils.generateUnixDate(null),
            artist_id: id
        }), ACC.Fetch.getData(url, ACC.Events.defaultFilters).then(response => response.json()).then(result => {
            const {data: data, success: success} = result;
            try {
                !0 === success ? ACC.EntityDetails.renderUpcomingData(data, renderTemplate, nonce) : ACC.Dashboard.showErrorNotification(result.error, 'alert');
            } catch (err) {
                console.log(err), ACC.Dashboard.showErrorNotification(err, 'alert');
            }
        }).catch(error => {
            ACC.Dashboard.showErrorNotification(error, 'alert'), console.log('error', error);
        }).finally(() => {
            ACC.EntityDetails.fetchData = !0;
        });
    },
    getMediaData: id => {
        const {action: action, nonce: nonce, url: url} = pj_my_channel, data = {
            action: action,
            nonce: nonce,
            owner_class: 'common\\models\\Artist',
            owner_id: id
        };
        ACC.Fetch.getData(url, data).then(response => response.json()).then(result => {
            const {output: output, success: success} = result, data = output.data;
            try {
                !0 === success ? ACC.EntityDetails.renderMyChannelData(data) : ACC.Dashboard.showErrorNotification(result.error, 'alert');
            } catch (err) {
                console.log(err), ACC.Dashboard.showErrorNotification(err, 'alert');
            }
        }).catch(error => {
            ACC.Dashboard.showErrorNotification(error, 'alert'), console.log('error', error);
        }).finally(() => {
            ACC.EntityDetails.fetchData = !0;
        });
    },
    renderUpcomingData: (data, renderTemplate, nonce) => {
        let content = '';
        if (data.length > 0) {
            content += '<ul class="picklejar-upcoming-list">';
            for (const item of data) content += `<li class="picklejar-upcoming-item">\n        <div class="picklejar-upcoming-img">\n            <img src="${item.banner.smallImage}" alt="event-${item.id}">\n        </div>\n        <div class="picklejar-upcoming-item-info">\n          <a href="${renderTemplate}?entityId=${item.id}&nonce=${nonce}" class="picklejar-upcoming-item-header">${item.name}</a>\n          <p class="picklejar-upcoming-item-address">${item.address.formatted_address}</p>\n          <small>${item.eventDates[0].dateTimeEnd}</small>\n          <small>${item.eventDates[0].dateTimeStart}</small>\n        </div>\n        </li>`;
            content += '</ul>';
        } else content += '<div class="m-4 w-100 picklejar-text-center">No Upcoming Events</div>';
        document.querySelector('#picklejar-upcoming').innerHTML = content;
    },
    renderMyChannelData: data => {
        let audioList = [], videoList = [], galleryList = [], mediaList = [];
        for (const mediaItem of data) if (mediaItem._misc) {
            const misc = mediaItem._misc;
            if (misc.media) {
                const media = misc.media;
                switch (!0) {
                  case media.type.includes('image'):
                    galleryList.push(media);
                    break;

                  case media.type.includes('video'):
                    videoList.push(media);
                    break;

                  case media.type.includes('audio'):
                    audioList.push(media);
                    break;

                  default:
                    mediaList.push(media);
                }
            }
        }
        ACC.EntityDetails.renderAudio(audioList), ACC.EntityDetails.renderVideo(videoList), 
        ACC.EntityDetails.renderGallery(galleryList), ACC.EntityDetails.renderMedia(mediaList);
    },
    renderAudio: audioList => {
        const galleryElement = document.getElementById('picklejar-listen-items');
        if (audioList.length) for (const audio of audioList) console.log('Audio', audio); else galleryElement.innerHTML = 'New Content Coming Soon';
    },
    renderVideo: videoList => {
        const galleryElement = document.getElementById('picklejar-watch-items');
        if (videoList.length) for (const video of videoList) console.log('Video: ', video); else galleryElement.innerHTML = 'New Content Coming Soon';
    },
    renderGallery: imageList => {
        const galleryElement = document.getElementById('picklejar-gallery-items');
        if (imageList.length) {
            let slides = '';
            for (const image of imageList) slides += `<div class="swiper-slide picklejar-gallery-item">\n        <div class="picklejar-gallery-img">\n            <img src="${image.original_image}" alt="${image.title}">\n        </div>\n        </div>`;
            let content = `<div class="picklejar-item-row picklejar-gallery-list">\n        <div class="swiper-button-prev picklejar-swiper-control"></div> \n        <div class="swiper picklejar-slider d-flex picklejar-gallery-list-swiper">\n            <div class="swiper-wrapper">${slides}</div>\n        </div>\n        <div class="swiper-button-next picklejar-swiper-control"></div>\n    </div>`;
            content += `\n    <div class="picklejar-item-row picklejar-gallery-thumbs">\n        <div class="swiper-button-prev picklejar-swiper-control"></div>\n        <div class="swiper picklejar-slider picklejar-gallery-thumbs-swiper">\n            <div class="swiper-wrapper">${slides}</div>\n        </div>\n        <div class="swiper-button-next picklejar-swiper-control"></div>\n    </div>`, 
            galleryElement.innerHTML = content;
            const swiperThumbs = new Swiper('.picklejar-gallery-thumbs-swiper', {
                spaceBetween: 10,
                slidesPerView: 4,
                freeMode: !0,
                watchSlidesProgress: !0
            });
            new Swiper('.picklejar-gallery-list-swiper', {
                spaceBetween: 10,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev'
                },
                thumbs: {
                    swiper: swiperThumbs
                }
            });
        } else galleryElement.innerHTML = 'New Content Coming Soon';
    },
    renderMedia: mediaList => {
        const galleryElement = document.getElementById('picklejar-watch-items');
        if (mediaList.length) for (const media of mediaList) console.log('Video: ', media); else galleryElement.innerHTML = 'New Content Coming Soon';
    }
}, ACC.Events = {
    _autoload: [],
    requestSliderId: null,
    eventsList: {},
    defaultFilters: {
        show_home_screen_events_only: 0,
        expired: 0,
        search_term: '',
        event_type: 'event,streaming',
        exclude_private: 1
    },
    eventsModel: data => ({
        id: data.id,
        categories: data.categories,
        name: data.name,
        message: data.message,
        address: data.address,
        phoneNumber: data.phoneNumber,
        email: data.email,
        avatar: data.avatar,
        banner: data.banner,
        isPrivate: data.isPrivate,
        isLive: data.isLive,
        isVerified: data.isVerified,
        eventAdmins: data.eventAdmins,
        eventParticipants: data.eventParticipants,
        genres: data.eventGenres,
        eventDates: data.eventDates,
        venue: data.venue,
        distance: data.distance,
        status: data.status,
        stramingLink: data.streamingLink,
        tipsEnabled: data.tipsEnabled,
        tipButtonId: data.tipButtonId,
        tipButton: data.tipButton,
        eventAccessType: data.eventAccessType,
        eventType: data.eventType,
        targetGoal: data.targetGoal,
        notesList: data.notesList,
        socialLinks: data.socialLinks,
        description: data.description,
        shortDescription: data.short_description,
        tags: data.tags,
        video: data.video,
        minAmount: data.min_amount,
        maxAmount: data.max_amount,
        predefinedAmounts: data.predefined_amounts,
        contributorAnonymous: data.contributor_anonymous,
        backerDetails: data.backerDetails,
        daysLeft: data.daysLeft,
        projectRewards: data.projectRewards,
        projectUpdates: data.projectUpdates,
        projectUrl: data.projectUrl
    }),
    getEvents: (target, pageSize, filters) => {
        if (target) ACC.Utils.getDataset(target, pageSize, filters); else {
            const eventsList = document.querySelectorAll('.picklejar-load-events .picklejar-entities-row');
            if (eventsList.length) for (let eventElement of eventsList) ACC.Utils.getDataset(eventElement, pageSize, filters);
        }
    },
    fetchEvents: async (pageSize, filters) => {
        const {action: action, nonce: nonce, url: url, entityFilter: entityFilter, renderTemplate: renderTemplate} = pj_event_list;
        ACC.EntityDetails.nonce = nonce;
        const defaultFilters = {
            action: action,
            nonce: nonce,
            start_date: ACC.Utils.generateUnixDate(null),
            page_size: pageSize || 12
        };
        return Object.assign(defaultFilters, ACC.Events.defaultFilters), ACC.Dashboard.entityDetailsPage = renderTemplate, 
        ACC.Fetch.fetchEntities(url, defaultFilters, entityFilter, filters);
    }
}, ACC.Fetch = {
    _autoload: [ [ 'getEntities', !document.querySelectorAll('.pj-filters-apply').length ] ],
    triggerButton: '',
    triggerButtonText: '',
    triggerButtonTarget: '',
    disableActionsClass: '.wrap, .picklejar-events-template, .picklejar-load-events',
    loaderTemplate: '<div class="picklejar-loader"><div class="picklejar-loading"></div></div>',
    loadMoreTemplate: '<button class="picklejar-btn picklejar-btn-primary pj-loading-button picklejar-load-more-btn">Load More</button>',
    controller: null,
    signal: null,
    currentElement: null,
    generateAbortController: () => {
        ACC.Fetch.controller = new AbortController(), ACC.Fetch.signal = ACC.Fetch.controller.signal;
    },
    checkAbortSignal: () => {
        ACC.Fetch.controller && ACC.Fetch.clearAbortSignal(), ACC.Fetch.generateAbortController();
    },
    clearAbortSignal: () => {
        ACC.Fetch.controller && (ACC.Fetch.controller.abort('Canceling Previous Call'), 
        ACC.Fetch.controller = null, ACC.Fetch.signal = null);
    },
    getData: async (url = '', data = {}, headers = {}, clearSignal = null) => {
        clearSignal && ACC.Fetch.checkAbortSignal();
        const urlWithParams = Object.keys(data).length ? `${url}?${new URLSearchParams(data).toString()}` : url, commonHeaders = {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer ${ACC.Dashboard.getCookie('pj_validation_token') || ACC.Login.accessToken}`,
            'Cache-Control': 'no-cache',
            Connection: 'keep-alive'
        };
        return Object.assign(commonHeaders, headers), await fetch(urlWithParams, {
            method: 'GET',
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: commonHeaders,
            signal: ACC.Fetch.signal
        });
    },
    postData: async (url = '', data = {}) => {
        return (await fetch(url, {
            method: 'POST',
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${ACC.Dashboard.getCookie('pj_validation_token') || ACC.Login.accessToken}`
            },
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify(data),
            signal: ACC.Fetch.signal
        })).json();
    },
    updateLoadingButtonText: () => {
        ACC.Fetch.triggerButton && (ACC.Fetch.triggerButton.innerHTML !== ACC.Fetch.loaderTemplate && (ACC.Fetch.triggerButtonText = ACC.Fetch.triggerButton.innerHTML), 
        ACC.Fetch.triggerButtonText.includes('material-symbols-outlined') ? ACC.Fetch.triggerButton.innerHTML = ACC.Fetch.loaderTemplate : ACC.Fetch.triggerButtonText.includes(ACC.Fetch.loaderTemplate) || (ACC.Fetch.triggerButton.innerHTML = ACC.Fetch.triggerButtonText + ACC.Fetch.loaderTemplate));
    },
    showLoading: target => {
        let eventRows = target;
        if (ACC.InfiniteScroll.enableInfiniteScroll = !1, target || (eventRows = document.querySelectorAll('#picklejar-layout-example-preview .picklejar-item-row'), 
        ACC.Fetch.triggerButtonTarget && (eventRows = document.querySelectorAll(ACC.Fetch.triggerButtonTarget))), 
        eventRows.length) for (let eventRow of eventRows) eventRow.innerHTML = ACC.Fetch.loaderTemplate;
        ACC.Fetch.updateLoadingButtonText(), target && ACC.InfiniteScroll.showInfiniteScrollLoader(target);
    },
    hideLoading: () => {
        if (ACC.Fetch.triggerButton.innerHTML = ACC.Fetch.triggerButtonText, ACC.Fetch.triggerButtonTarget = '', 
        ACC.InfiniteScroll.infiniteScrollLoader.length) for (const loader of ACC.InfiniteScroll.infiniteScrollLoader) loader.innerHTML = '';
        if (!document.querySelector('#picklejar-layout-example-preview')) {
            const infiniteScrollList = document.querySelectorAll('.picklejar-infinite-scroll:not([data-infinite-scroll-enabled="true"])');
            if (infiniteScrollList.length) {
                for (let infiniteScroll of infiniteScrollList) infiniteScroll.setAttribute('data-infinite-scroll-enabled', 'true'), 
                infiniteScroll.classList.remove('picklejar-hidden');
                ACC.InfiniteScroll.infiniteScroll();
            }
        }
    },
    enableActions: currentEvent => {
        jQuery(ACC.Fetch.disableActionsClass).find('button, input, textArea , select').prop('disabled', !1), 
        ACC.Fetch.hideLoading(currentEvent);
    },
    disableActions: (currentEvent = null) => {
        ACC.InfiniteScroll.enableInfinityScroll = !1, document.querySelector('#wpbody') && jQuery(ACC.Fetch.disableActionsClass).find('button, input, textArea, select').prop('disabled', !0), 
        ACC.Fetch.showLoading(currentEvent);
    },
    getEntities: (target, pageSize, filters) => {
        if (target) ACC.Utils.getDataset(target, pageSize, filters); else {
            const eventsList = document.querySelectorAll('.picklejar-load-events .picklejar-entities-row');
            if (eventsList.length) for (let eventElement of eventsList) ACC.Utils.getDataset(eventElement, pageSize, filters);
        }
    },
    queryEntities: (currentElement, elementPageSize, elementFilters, elementStyles) => {
        ACC.Fetch.disableActions(currentElement);
        const formattedResponse = {};
        let action, errorMessage;
        switch (currentElement.dataset.pjEntity) {
          case 'event':
            action = ACC.Events.fetchEvents(elementPageSize, elementFilters), errorMessage = ACC.Messages.noEventsFoundMessage;
            break;

          case 'artist':
            action = ACC.Artist.fetchArtists(elementPageSize, elementFilters), errorMessage = ACC.Messages.noArtistsFoundMessage;
        }
        return action.then(rawData => (formattedResponse.currentPage = rawData.headers.get('X-Pagination-Current-Page'), 
        formattedResponse.totalPages = rawData.headers.get('X-Pagination-Page-Count'), rawData.json())).then(response => {
            try {
                let {success: success, data: data} = response;
                if (!0 === success) if (data.length) {
                    ACC.Utils.updateDataFilters(currentElement, formattedResponse, elementFilters);
                    const eventParametersStyles = elementStyles;
                    let eventContent = '';
                    if ('slider' === eventParametersStyles.layout) {
                        eventContent += ACC.Render.renderSliderTemplate(data, eventParametersStyles);
                        const swiperSlider = ACC.Swiper.sliders[ACC.Events.requestSliderId];
                        ACC.InfiniteScroll.loadMore && swiperSlider ? (swiperSlider.appendSlide(eventContent), 
                        swiperSlider.allowSlideNext = !0, swiperSlider.allowTouchMove = !0, swiperSlider.slideNext()) : (currentElement.innerHTML = eventContent, 
                        ACC.Swiper.initPicklejarSlider(null)), ACC.Events.requestSliderId = null;
                    } else eventContent += ACC.Render.renderGridTemplate(data, eventParametersStyles), 
                    ACC.InfiniteScroll.loadMore ? currentElement.innerHTML += eventContent : currentElement.innerHTML = eventContent;
                } else currentElement.innerHTML = errorMessage; else ACC.Dashboard.showErrorNotification(response.error, 'alert');
            } catch (error) {
                console.log('error', error), ACC.Dashboard.showErrorNotification(error, 'alert');
            }
        }).catch(error => {
            console.log('error', error), ACC.Dashboard.showErrorNotification(error, 'alert');
        }).finally(() => {
            ACC.Fetch.enableActions(currentElement), ACC.InfiniteScroll.infiniteScrollLoader && (ACC.InfiniteScroll.enableInfiniteScroll = !0, 
            ACC.InfiniteScroll.loadMore = !1);
            const customizeInputList = document.querySelectorAll('#event_parameters_settings_layout .picklejar-form-control input');
            if (customizeInputList.length) for (const customizeInput of customizeInputList) customizeInput.dispatchEvent(new Event('change'));
            ACC.InfiniteScroll.activeScrollId = ACC.InfiniteScroll.activeScrollId.filter(item => item !== currentElement.id);
        });
    },
    fetchEntities: (url, defaultFilters, entityFilter, filters) => {
        entityFilter.Admin ? (ACC.Dashboard.getLocation(), ACC.Dashboard.coords.latitude && (defaultFilters.lat = ACC.Dashboard.coords.latitude), 
        ACC.Dashboard.coords.longitude && (defaultFilters.long = ACC.Dashboard.coords.longitude)) : Object.assign(defaultFilters, entityFilter);
        const query = Object.assign(defaultFilters, filters || {});
        return ACC.Fetch.getData(url, query, {}, !0);
    }
}, ACC.FrontendFilters = {
    _autoload: [ 'triggerModals', 'triggerApplyFilters', 'bindClearFilterButtons', [ 'fetchFrontendCategoriesFilterOptions', document.querySelectorAll('.picklejar-filters-genre').length ], [ 'triggerToggleDropDown', document.querySelectorAll('.picklejar-dropdown-toggle').length ], [ 'triggerGeolocationAutocomplete', document.querySelectorAll('.picklejar-trigger-geolocation-autocomplete').length ], [ 'triggerGetCurrentLocation', document.querySelectorAll('.picklejar-get-current-location').length ] ],
    modalList: {},
    geolocationQuery: '',
    defaultValues: {
        city: '',
        stateCode: '',
        countryCode: '',
        postalCode: ''
    },
    triggerModals: () => {
        const triggerList = document.querySelectorAll('[data-pj-modal]');
        for (const triggerElement of triggerList) triggerElement.addEventListener('click', trigger => {
            trigger.target.blur();
            const targetDataset = trigger.currentTarget.dataset.pjModal;
            if (targetDataset) {
                const targetModal = document.querySelector(targetDataset);
                if (targetModal) {
                    const modalList = ACC.FrontendFilters.modalList;
                    let modalKey = '';
                    if (!ACC.FrontendFilters.modalList[targetDataset]) {
                        const openedModal = new Modal({
                            el: targetModal
                        });
                        modalList[modalKey = targetDataset.replace('#', '')] = openedModal, ACC.FrontendFilters.triggerClearAllModalFilters(targetModal);
                    }
                    ACC.FrontendFilters.triggerModalEvents(modalKey);
                }
            }
        });
    },
    triggerModalEvents: modalKey => {
        const modalList = ACC.FrontendFilters.modalList;
        let defaultGenreSelected = [], defaultDateSelected = [], defaultLocationText = null, defaultLocationSelected = ACC.FrontendFilters.defaultValues;
        modalList[modalKey].on('show', currentModal => {
            defaultGenreSelected = currentModal.el.querySelectorAll('.picklejar-filters-genre:checked');
            const clearGenreButton = document.querySelector('.picklejar-clear-genre');
            clearGenreButton && (0 < defaultGenreSelected.length ? clearGenreButton.classList.remove('picklejar-hidden') : clearGenreButton.classList.add('picklejar-hidden')), 
            defaultDateSelected = currentModal.el.querySelectorAll('.picklejar-filters-dates:checked'), 
            (defaultLocationText = currentModal.el.querySelector('.picklejar-trigger-geolocation-autocomplete')) && (ACC.FrontendFilters.defaultValues.city = document.querySelector('[name="city"]').value, 
            ACC.FrontendFilters.defaultValues.stateCode = document.querySelector('[name="state"]').value, 
            ACC.FrontendFilters.defaultValues.countryCode = document.querySelector('[name="country"]').value, 
            ACC.FrontendFilters.defaultValues.countryCode = document.querySelector('[name="zip"]').value);
        }).on('dismiss', () => {
            for (const selected of defaultGenreSelected) selected.checked = !0;
            for (const selectedDate of defaultDateSelected) selectedDate.checked = !0;
            ACC.FrontendFilters.updateAddressFields(defaultLocationSelected);
        }), modalList[modalKey].show();
    },
    triggerClearAllModalFilters: targetModal => {
        targetModal.querySelector('.picklejar-clear-all-btn').addEventListener('click', event => {
            ACC.FrontendFilters.clearAllModalFilters(event);
        });
    },
    clearAllModalFilters: event => {
        const clearGenre = document.querySelector('.picklejar-clear-genre');
        clearGenre && clearGenre.click();
        const clearLocation = document.querySelector('.picklejar-clear-location');
        clearLocation && clearLocation.click();
        const clearDate = document.querySelector('.picklejar-filters-date:checked');
        clearDate && (clearDate.checked = !1), event.target.classList.contains('picklejar-reset-filters') && document.querySelector('.picklejar-btn-apply').click();
    },
    bindClearFilterButtons: () => {
        const filterForm = document.querySelector('.picklejar-entity-filter-form');
        filterForm && filterForm.addEventListener('submit', event => event.preventDefault());
        const clearGenre = document.querySelector('.picklejar-clear-genre');
        clearGenre && clearGenre.addEventListener('click', () => {
            clearGenre.classList.add('picklejar-hidden'), ACC.FrontendFilters.clearCheckboxRadioFilters('#picklejar-modal-filter-list', '.picklejar-filters-genre');
        });
        const clearLocation = document.querySelector('.picklejar-clear-location');
        clearLocation && clearLocation.addEventListener('click', () => {
            clearLocation.classList.add('picklejar-hidden'), ACC.FrontendFilters.clearTextFilter('#picklejar-modal-filter-list', '.picklejar-filters-location'), 
            ACC.FrontendFilters.clearLocationValues();
        });
        const clearDate = document.querySelector('.picklejar-clear-date');
        clearDate && clearDate.addEventListener('click', () => {
            clearDate.classList.add('picklejar-hidden'), ACC.FrontendFilters.clearCheckboxRadioFilters('#picklejar-modal-filter-list', '.picklejar-filters-date');
        });
    },
    clearCheckboxRadioFilters: (targetModal, targetClass) => {
        for (const checkboxInput of document.querySelectorAll(targetModal + ' ' + targetClass + ' input[type="checkbox"]')) checkboxInput.checked = !1;
        for (const checkboxInput of document.querySelectorAll(targetModal + ' ' + targetClass + ' input[type="radio"]')) checkboxInput.checked = !1;
    },
    clearTextFilter: (targetModal, targetClass) => {
        for (const inputText of document.querySelectorAll(targetModal + ' ' + targetClass + ' input[type="text"]')) inputText.value = '';
        for (const hiddenText of document.querySelectorAll(targetModal + ' ' + targetClass + ' input[type="hidden"]')) hiddenText.value = '';
    },
    triggerApplyFilters: () => {
        const applyButtons = document.querySelectorAll('.picklejar-btn-apply');
        for (const triggerButton of applyButtons) triggerButton.addEventListener('click', () => {
            const modalId = triggerButton.closest('.picklejar-modal').getAttribute('id');
            ACC.FrontendFilters.modalList[modalId].hide(), ACC.FrontendFilters.updateAppliedFilters();
        });
    },
    triggerGetCurrentLocation: () => {
        const errorMessageNode = document.querySelector('.picklejar-geolocation-error');
        for (const getLocationButton of document.querySelectorAll('.picklejar-get-current-location')) getLocationButton.addEventListener('click', event => {
            errorMessageNode && errorMessageNode.classList.add('picklejar-hidden'), navigator.geolocation && navigator.geolocation.getCurrentPosition(position => {
                ACC.Dashboard.coords = position.coords;
            }, error => {
                event.target.classList.add('picklejar-hidden'), errorMessageNode && (errorMessageNode.innerHTML = error.message, 
                errorMessageNode.classList.remove('picklejar-hidden')), console.log('An error occurred... The error code and message are: ' + error.code + '/' + error.message);
            });
        });
    },
    triggerLocationReverseQuery: () => {
        const {nonce: nonce, action: action, url: url} = pj_geolocation_reverse_query, data = {
            nonce: nonce,
            action: action,
            latitude: ACC.Dashboard.coords.latitude,
            longitude: ACC.Dashboard.coords.longitude
        };
        ACC.FrontendFilters.fetchGeolocation(url, data);
    },
    loadAutocompleteGeolocation: () => {
        if (0 < ACC.FrontendFilters.geolocationQuery.value.length) {
            const {nonce: nonce, action: action, url: url} = pj_geolocation_autocomplete, data = {
                nonce: nonce,
                action: action,
                q: ACC.FrontendFilters.geolocationQuery.value
            };
            ACC.FrontendFilters.fetchGeolocation(url, data);
        } else ACC.FrontendFilters.geolocationQuery.value = '', ACC.FrontendFilters.clearLocationValues();
    },
    fetchGeolocation: (url, data) => {
        document.querySelector('.picklejar-geolocation-results').innerHTML = '<li class="picklejar-list-item"><div class="picklejar-loader"><div class="picklejar-loading"></div></div></li>', 
        ACC.Fetch.getData(url, data).then(response => response.json()).then(result => {
            ACC.FrontendFilters.renderGeolocationItemList(result);
        });
    },
    renderGeolocationItemList: result => {
        const errorMessageNode = document.querySelector('.picklejar-geolocation-error');
        if (errorMessageNode && errorMessageNode.classList.add('picklejar-hidden'), 'OK' === result.message) {
            let geolocationResult = '';
            if (result.output && 0 < result.output.length) {
                for (const location of result.output) geolocationResult += ACC.FrontendFilters.createGeolocationItem(location);
                document.querySelector('.picklejar-geolocation-results').innerHTML = geolocationResult;
                for (const itemResult of document.querySelectorAll('.picklejar-location-item-result')) itemResult.addEventListener('click', event => {
                    document.querySelector('.picklejar-trigger-geolocation-autocomplete').value = event.target.innerHTML, 
                    ACC.FrontendFilters.clearLocationValues(), ACC.FrontendFilters.updateAddressFields(JSON.parse(event.target.dataset.location));
                });
            } else document.querySelector('.picklejar-geolocation-results').innerHTML = '<li class="picklejar-list-item">No results</li>', 
            ACC.FrontendFilters.updateAddressFields(ACC.FrontendFilters.defaultValues);
        }
    },
    clearLocationValues: () => {
        document.querySelector('.picklejar-geolocation-results').innerHTML = '', ACC.FrontendFilters.defaultValues = {
            city: '',
            stateCode: '',
            countryCode: '',
            postalCode: ''
        }, ACC.FrontendFilters.updateAddressFields(ACC.FrontendFilters.defaultValues);
    },
    triggerGeolocationAutocomplete: () => {
        const autocompleteInputList = document.querySelectorAll('.picklejar-trigger-geolocation-autocomplete'), clearLocationButton = document.querySelector('.picklejar-clear-location');
        for (const autocompleteInput of autocompleteInputList) autocompleteInput.addEventListener('keyup', event => {
            'Shift' !== event.key && 'Meta' !== event.key && 'Control' !== event.key && (ACC.FrontendFilters.geolocationQuery = event.target, 
            ACC.Debounce.debounceFunction(ACC.FrontendFilters.loadAutocompleteGeolocation, ACC.Debounce.debounceTimeout), 
            ACC.FrontendFilters.hideClearLocationButton(ACC.FrontendFilters.geolocationQuery.value, clearLocationButton));
        });
    },
    hideClearLocationButton: (value, button) => {
        button && (value.length ? button.classList.remove('picklejar-hidden') : button.classList.add('picklejar-hidden'));
    },
    updateAddressFields: location => {
        const cityInput = document.querySelector('[name="city"]'), stateInput = document.querySelector('[name="state"]'), countryInput = document.querySelector('[name="country"]'), zipInput = document.querySelector('[name="zip"]');
        cityInput.value = location.city, stateInput.value = location.stateCode, countryInput.value = location.countryCode, 
        zipInput.value = location.postalCode;
    },
    updateAppliedFilters: () => {
        let appliedFilters = '', counter = 0;
        const selectedDate = document.querySelector('.picklejar-filters-date:checked'), locationAutocomplete = document.querySelector('.picklejar-trigger-geolocation-autocomplete'), appliedGenreFilters = document.querySelectorAll('.picklejar-filters-genre:checked');
        selectedDate && 0 < selectedDate.value.length && (appliedFilters += ACC.FrontendFilters.createChip(`<span class="material-symbols-outlined">calendar_month</span> ${selectedDate.nextElementSibling.innerHTML.trim()}`, selectedDate.getAttribute('id')), 
        counter++), locationAutocomplete && 0 < locationAutocomplete.value.length && (appliedFilters += ACC.FrontendFilters.createChip(`<span class="material-symbols-outlined">location_on</span> ${locationAutocomplete.value.trim()}`, 'locationFilter'), 
        counter++);
        for (const genreFilters of appliedGenreFilters) appliedFilters += ACC.FrontendFilters.createChip(`<span class="material-symbols-outlined">assistant</span>${genreFilters.previousElementSibling.innerHTML}`, genreFilters.getAttribute('id')), 
        counter++;
        const appliedFilterListItems = document.querySelectorAll('.picklejar-applied-filters-list');
        for (const element of appliedFilterListItems) {
            const clearAllButton = document.querySelector('.picklejar-search-entity-filter-form  .picklejar-clear-all-btn').cloneNode(!0);
            clearAllButton.classList.add('picklejar-reset-filters'), element.innerHTML = appliedFilters.length ? `<div class="picklejar-d-flex picklejar-space-between"><p>(${counter}) Applied Filters:</p> ${clearAllButton.outerHTML}</div> ${appliedFilters}` : appliedFilters;
            const triggerClearButton = document.querySelector('.picklejar-reset-filters');
            triggerClearButton && triggerClearButton.addEventListener('click', event => {
                ACC.FrontendFilters.clearAllModalFilters(event);
            });
        }
        for (const clearFilter of document.querySelectorAll('[data-remove-filter]')) clearFilter.addEventListener('click', () => {
            const targetId = clearFilter.dataset.removeFilter;
            if (targetId) {
                if ('locationFilter' === targetId) {
                    const clearLocation = document.querySelector('.picklejar-clear-location');
                    clearLocation && clearLocation.click();
                } else document.getElementById(targetId).checked = !1;
                const applyButton = document.querySelector('.picklejar-btn-apply');
                applyButton && applyButton.click();
            }
        });
    },
    triggerToggleDropDown: () => {
        const dropdownItems = document.querySelectorAll('.picklejar-dropdown-toggle');
        for (const dropdownItem of dropdownItems) dropdownItem.addEventListener('click', () => {
            dropdownItem.closest('.picklejar-dropdown').classList.toggle('picklejar-show');
        });
    },
    fetchFrontendCategoriesFilterOptions: () => {
        ACC.FrontendFilters.updateSelectOptions([ 'genre' ], '.picklejar-filters-genre', pj_music_genre);
    },
    updateSelectOptions: (entityList, target, targetUrl) => {
        let completedRequests = 0, optionsHtml = '';
        ACC.Fetch.disableActions();
        for (const entity of entityList) {
            const {nonce: nonce, action: action, url: url} = targetUrl, data = {
                nonce: nonce,
                action: action,
                entity: entity
            };
            ACC.Fetch.getData(url, data).then(response => response.json()).then(content => {
                if (!0 === content.success) {
                    const entityLowercase = entity.toLowerCase();
                    completedRequests++;
                    const data = content.output || content.data, entityInputSelect = document.querySelectorAll(target);
                    if (data) {
                        Object.keys(data).map(id => {
                            if (data[id].genreName) {
                                const inputId = `${entity}-${id}`;
                                optionsHtml += `\n                    <li class="picklejar-list-item picklejar-pointer">\n                        <label for="${inputId}" class="picklejar-form-group picklejar-form-check-input">\n                          <div>${data[id].genreName}</div>\n                          <input \n                              type="checkbox" \n                              class="picklejar-form-control picklejar-filters-${entity}" \n                              id="${inputId}" \n                              value="${data[id].id}" \n                              name="${'event' === entityInputSelect[0].dataset.pjEntity ? 'event_genres[]' : 'genres[]'}"\n                            />\n                      </div>\n                    </li>\n                `;
                            }
                        });
                        const selectTarget = document.querySelector(`.picklejar-filters-${entityLowercase}`);
                        if (selectTarget) {
                            selectTarget.innerHTML = optionsHtml;
                            const clearEntityButton = document.querySelector(`.picklejar-clear-${entityLowercase}`);
                            for (const itemList of document.querySelectorAll(`.picklejar-filters-${entityLowercase}`)) itemList.addEventListener('change', () => {
                                0 < document.querySelectorAll(`.picklejar-filters-${entityLowercase}:checked`).length ? clearEntityButton.classList.remove('picklejar-hidden') : clearEntityButton.classList.add('picklejar-hidden');
                            });
                        }
                        if (completedRequests === entityList.length && entityInputSelect.length) {
                            const selectInput = entityInputSelect[0];
                            selectInput.innerHTML = optionsHtml, selectInput.value = selectInput.dataset.selected || selectInput.dataset.defaultValue, 
                            selectInput.dispatchEvent(new Event('change'));
                            const counter = document.querySelector(`.picklejar-entity-type-${entity}-counter`);
                            counter && (counter.innerHTML = Object.entries(data).length.toString());
                        }
                    } else {
                        ACC.Dashboard.showWarningNotification({
                            message: 'No results found'
                        });
                        const counter = document.querySelector(`.picklejar-entity-type-${entity}-counter`);
                        counter && (counter.innerHTML = '0');
                    }
                } else ACC.Dashboard.showErrorNotification({
                    message: content.message || content.error.message
                }, 'alert');
            }).finally(() => {
                ACC.Fetch.enableActions(), ACC.LayoutConfig.triggerEntities++, ACC.LayoutConfig.triggerEntities === ACC.LayoutConfig.totalEntities && document.querySelector('.pj-filters-apply').click();
            });
        }
    },
    createGeolocationItem: location => `<li class="picklejar-list-item picklejar-hover picklejar-location-item-result" data-location='${JSON.stringify(location)}'>${location.formattedAddress}</li>`,
    createChip: (value, id) => `<div class="picklejar-chip">${value}<button class="picklejar-btn picklejar-btn-link" data-remove-filter="${id}"><span class="material-symbols-outlined">close</span></button></div>`
}, ACC.InfiniteScroll = {
    _autoload: [],
    infiniteScrollActive: !1,
    enableInfiniteScroll: !0,
    loadMore: !1,
    throttleTimer: null,
    infiniteScrollLoader: [],
    infiniteScrollLoaderList: {},
    infiniteScrollLoaderGridList: [],
    activeScrollId: [],
    lastScrollPosition: 0,
    infiniteScroll: () => {
        if (!document.getElementById('picklejar-layout-example-preview')) {
            const container = document.querySelectorAll('.picklejar-grid-layout');
            if (ACC.InfiniteScroll.infiniteScrollLoader = document.querySelectorAll('.picklejar-infinite-scroll[data-infinite-scroll-enabled="true"]'), 
            container.length) if (container.length > 1) {
                const gridInfiniteScrollList = document.querySelectorAll('.picklejar-infinite-scroll.grid');
                for (let gridInfiniteScroll of gridInfiniteScrollList) gridInfiniteScroll.classList.remove('picklejar-infinite-scroll', 'picklejar-hidden'), 
                gridInfiniteScroll.classList.add('picklejar-load-more', 'picklejar-text-center', 'picklejar-form-group'), 
                gridInfiniteScroll.innerHTML = ACC.Fetch.loadMoreTemplate;
                ACC.InfiniteScroll.infiniteScrollActive || (ACC.Dashboard.bindShowLoading(), ACC.InfiniteScroll.bindLoadMoreButton(), 
                ACC.InfiniteScroll.infiniteScrollActive = !0);
            } else {
                for (let scrollLoader of ACC.InfiniteScroll.infiniteScrollLoader) if (scrollLoader.classList.contains('grid')) {
                    const loaderId = scrollLoader.getAttribute('id');
                    ACC.InfiniteScroll.infiniteScrollLoaderList[loaderId] = scrollLoader, ACC.InfiniteScroll.infiniteScrollLoaderGridList.push(loaderId);
                }
                window.addEventListener('scroll', ACC.InfiniteScroll.handleInfiniteScroll, {
                    passive: !0
                });
            }
        }
    },
    handleInfiniteScroll: () => {
        let currentScrollPosition = document.documentElement.scrollTop || document.body.scrollTop;
        currentScrollPosition > 0 && ACC.InfiniteScroll.lastScrollPosition <= currentScrollPosition && !ACC.InfiniteScroll.infiniteScrollLoaderGridList.includes(ACC.InfiniteScroll.activeScrollId) && Object.values(ACC.InfiniteScroll.infiniteScrollLoaderList).forEach(htmlElement => {
            ACC.InfiniteScroll.isInViewport(htmlElement) && ACC.InfiniteScroll.enableInfiniteScroll && (ACC.InfiniteScroll.loadMore = !0, 
            ACC.Throttle.throttleFunction(ACC.Fetch.getEntities, ACC.Throttle.defaultThrottleTime, htmlElement));
        }), currentScrollPosition >= ACC.InfiniteScroll.lastScrollPosition && (ACC.InfiniteScroll.lastScrollPosition = currentScrollPosition);
    },
    bindLoadMoreButton: () => {
        const loadMoreButtonList = document.querySelectorAll('.picklejar-load-more-btn');
        for (const loadMoreButton of loadMoreButtonList) loadMoreButton.addEventListener('click', () => {
            ACC.Fetch.triggerButton = loadMoreButton;
            const entityId = loadMoreButton.closest('.picklejar-load-more').dataset.pjEntityTarget;
            ACC.InfiniteScroll.loadMore = !0, ACC.Events.getEvents(document.querySelector(entityId), null, null);
        });
    },
    removeInfiniteScroll: () => {
        ACC.InfiniteScroll.loader.remove(), window.removeEventListener('scroll', ACC.InfiniteScroll.handleInfiniteScroll);
    },
    isInViewport: element => {
        const rect = element.getBoundingClientRect();
        return rect.top > 0 && rect.left >= 0 && rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && rect.right <= (window.innerWidth || document.documentElement.clientWidth);
    },
    showInfiniteScrollLoader: target => {
        const loaderContainer = target.closest('.picklejar-load-events').querySelector('[data-infinite-scroll-enabled]');
        loaderContainer && (loaderContainer.innerHTML = ACC.Fetch.loaderTemplate);
    }
}, ACC.LayoutConfig = {
    _autoload: [ [ 'bindAddReduce', document.querySelectorAll('input[type=number]').length ], [ 'bindClearColor', document.querySelectorAll('.pj-clear-color').length ], 'bindCloneTemplate', [ 'bindDatePicker', document.querySelectorAll('input[type=date].pj-filters').length ], [ 'bindUpdateFilters', document.querySelectorAll('.pj-filters-apply').length ], 'bindStylingInputChanges', [ 'bindSearchFilterField', document.querySelectorAll('.picklejar-filter-search-term-input').length ], [ 'bindSelectDropdown', document.querySelectorAll('.picklejar-select-drop-down').length ], [ 'bindSearchFilterFieldKeyUp', document.querySelectorAll('.picklejar-filter-search-term-input').length ], [ 'bindTextAlignment', document.querySelectorAll('.picklejar-text-alignment').length ], [ 'fetchArtistsAndBandsFilterOptions', document.querySelectorAll('.picklejar-filters-artists-band').length ], [ 'fetchVenuesFilterOptions', document.querySelectorAll('.picklejar-filters-venue').length ], [ 'fetchCategoriesFilterOptions', document.querySelectorAll('.pj-filters-category').length ], 'saveAndReload' ],
    eventTemplate: null,
    totalEntities: 0,
    triggerEntities: 0,
    searchField: null,
    inputStylingValues: {
        '.pj-title-label-events': {
            function: 'updateContent',
            target: '.picklejar-title',
            event: 'keyup',
            defaultValue: 'Events',
            property: 'innerHTML'
        },
        '.pj-title-label-artist': {
            function: 'updateContent',
            target: '.picklejar-title',
            event: 'keyup',
            defaultValue: 'Artists',
            property: 'innerHTML'
        },
        '.pj-title-color': {
            function: 'updateContent',
            target: '.picklejar-title',
            event: 'change',
            defaultValue: '#999',
            property: 'color'
        },
        '.pj-date-background-color': {
            function: 'updateContent',
            target: '.picklejar-event-date',
            event: 'change',
            defaultValue: '#999',
            property: 'backgroundColor'
        },
        '.pj-date-text-color': {
            function: 'updateContent',
            target: '.picklejar-event-date',
            event: 'change',
            defaultValue: '#999',
            property: 'color'
        },
        '.pj-avatar-profile-border-color': {
            function: 'updateContent',
            target: '.picklejar-avatar',
            event: 'change',
            defaultValue: '#999',
            property: 'borderColor'
        },
        '.pj-avatar-profile-text-color': {
            function: 'updateContent',
            target: '.picklejar-contact',
            event: 'change',
            defaultValue: '#999',
            property: 'color'
        },
        '.pj-slider-control-color': {
            function: 'updateContent',
            target: '.picklejar-swiper-control',
            event: 'change',
            defaultValue: '#999',
            property: 'color'
        },
        '.pj-location-text-color': {
            function: 'updateContent',
            target: '.picklejar-location',
            event: 'change',
            defaultValue: '#FDA901',
            property: 'color'
        },
        '.pj-items-to-show': {
            function: 'updateGrid',
            target: '.picklejar-entity-column',
            event: 'change',
            defaultValue: '12'
        }
    },
    bindAddReduce: () => {
        const inputNumberList = document.querySelectorAll('input[type=number]');
        if (inputNumberList.length) for (const currentInput of inputNumberList) {
            const currentInputGroup = currentInput.closest('.picklejar-input-group');
            if (document.querySelectorAll(ACC.Swiper.sliderClass).length) {
                const itemsToShowInput = document.querySelector('input[type=number].pj-items-to-show'), slidesToShow = ACC.Swiper.slidesToShow;
                itemsToShowInput.value = Math.ceil(itemsToShowInput.value / slidesToShow) * slidesToShow, 
                itemsToShowInput.dispatchEvent(new Event('blur'));
            }
            if (currentInputGroup) {
                const addButton = currentInputGroup.querySelectorAll('.picklejar-add'), removeButton = currentInputGroup.querySelectorAll('.picklejar-remove');
                addButton && addButton[0].addEventListener('click', () => ACC.LayoutConfig.updateInputNumber(currentInput, 'add')), 
                removeButton && removeButton[0].addEventListener('click', () => ACC.LayoutConfig.updateInputNumber(currentInput, 'minus')), 
                currentInput.addEventListener('blur', () => ACC.LayoutConfig.updateInputNumber(currentInput, 'blur'));
            }
        }
    },
    bindClearColor: () => {
        const clearColorButtons = document.querySelectorAll('.pj-clear-color');
        if (clearColorButtons) for (let clearButton of clearColorButtons) clearButton.addEventListener('click', () => {
            const $colorInput = jQuery(clearButton).closest('.picklejar-form-group').find('.picklejar-form-control'), defaultValue = $colorInput.data('default');
            $colorInput.val(defaultValue), $colorInput.trigger('change');
        });
    },
    bindCloneTemplate: () => {
        if (!ACC.LayoutConfig.eventTemplate) {
            const eventCol = document.querySelector('#picklejar-preview .picklejar-entities-row');
            if (eventCol) {
                const swiperElement = eventCol.querySelector('.swiper-wrapper');
                ACC.LayoutConfig.eventTemplate = swiperElement ? swiperElement.innerHTML.toString() : eventCol.innerHTML;
            }
        }
    },
    bindStylingInputChanges: () => {
        Object.keys(ACC.LayoutConfig.inputStylingValues).forEach(inputKey => {
            const currentKey = ACC.LayoutConfig.inputStylingValues[inputKey], inputList = document.querySelectorAll(inputKey);
            if (inputList.length) {
                inputList.forEach(input => {
                    input.addEventListener(currentKey.event, inputValue => {
                        const updateValue = inputValue.target.value || currentKey.defaultValue;
                        ACC.LayoutConfig[currentKey.function](input, currentKey.target, updateValue, currentKey.property);
                    }), input.addEventListener('input', inputValue => {
                        const updateValue = inputValue.target.value || currentKey.defaultValue;
                        ACC.LayoutConfig[currentKey.function](input, currentKey.target, updateValue, currentKey.property);
                    });
                });
                const sliderControl = document.querySelector('.picklejar-slider-form-control');
                sliderControl && document.querySelectorAll('.picklejar-grid-layout').length && sliderControl.classList.add('picklejar-hidden');
            }
        });
    },
    bindUpdateFilters: () => {
        ACC.LayoutConfig.totalEntities = document.querySelectorAll('.pj-trigger-apply-filter').length;
        const applyFilterButton = document.querySelector('.pj-filters-apply');
        if (applyFilterButton) {
            let pageSize = 12, queryParams = {};
            const inputItemsToShow = document.querySelector('.pj-items-to-show');
            inputItemsToShow && (pageSize = inputItemsToShow.value), applyFilterButton.addEventListener('click', () => {
                ACC.Fetch.triggerButton = applyFilterButton;
                const filtersInputLust = document.querySelectorAll('.pj-filters');
                for (let filterInput of filtersInputLust) {
                    const filterDataset = filterInput.dataset.filter;
                    filterDataset && (!filterInput.value || 'start_date' !== filterDataset && 'end_date' !== filterDataset ? queryParams[filterDataset] = filterInput.value : queryParams[filterDataset] = ACC.Utils.generateUnixDate(filterInput.value));
                }
                ACC.Fetch.getEntities(null, pageSize, queryParams);
            }), 0 === ACC.LayoutConfig.totalEntities && setTimeout(() => {
                applyFilterButton.click();
            }, 3500);
        }
    },
    bindSearchFilterField: () => {
        const picklejarEventsFilterForms = document.querySelectorAll('.picklejar-search-entity-filter-form'), eventsFilterForm = document.querySelector('.picklejar-entity-filter-form');
        eventsFilterForm && eventsFilterForm.addEventListener('submit', e => e.preventDefault());
        for (let picklejarEventsFilterForm of picklejarEventsFilterForms) {
            const searchSubmitButtonList = picklejarEventsFilterForm.querySelectorAll('.picklejar-search-filter-submit');
            for (const searchSubmitButton of searchSubmitButtonList) searchSubmitButton.addEventListener('click', () => {
                ACC.InfiniteScroll.lastScrollPosition = 0, document.querySelector('.picklejar-entities-row').innerHTML = '';
                const element = document.querySelector('.picklejar-entities-row');
                element && window.scroll({
                    top: element.offsetTop,
                    left: 0,
                    behavior: 'smooth'
                }), ACC.LayoutConfig.triggerSubmitFilterSearch();
            });
        }
    },
    bindSearchFilterFieldKeyUp: () => {
        const picklejarSearchEventsFilterInputs = document.querySelectorAll('.picklejar-filter-search-term-input');
        if (picklejarSearchEventsFilterInputs.length) for (let picklejarSearchEventsFilterInput of picklejarSearchEventsFilterInputs) picklejarSearchEventsFilterInput.addEventListener('keyup', element => {
            ACC.LayoutConfig.searchField = element.target, ACC.Debounce.debounceFunction(ACC.LayoutConfig.triggerFilterSearch, ACC.Debounce.debounceTimeout);
        });
    },
    triggerFilterSearch: () => {
        const searchFilterButton = ACC.LayoutConfig.searchField.closest('.picklejar-search-entity-filter-form').querySelector('.picklejar-search-filter-submit');
        searchFilterButton && searchFilterButton.click();
    },
    triggerSubmitFilterSearch: () => {
        const filterForm = document.querySelector('.picklejar-entity-filter-form');
        if (filterForm) {
            const searchInput = document.querySelector('.picklejar-filter-search-term-input'), trimValue = searchInput.value.trim(), filters = {
                [searchInput.getAttribute('name')]: trimValue
            }, formData = new FormData(filterForm);
            let currentKey, sameKey = 0;
            for (const formDataValues of formData.entries()) {
                const key = formDataValues[0], value = formDataValues[1];
                let targetKey = key;
                if (value) switch (!0) {
                  case 'start_date' === targetKey || 'end_date' === targetKey:
                    filters[targetKey] = ACC.Utils.generateUnixDate(value);
                    break;

                  case key.includes('[]'):
                    {
                        currentKey = key;
                        const formatedKey = key.replace('[]', '');
                        key.includes('event_genres') ? (targetKey = `${formatedKey}[${sameKey}]`, currentKey === key ? sameKey++ : sameKey = 0, 
                        filters[targetKey] = value) : filters[targetKey = formatedKey] && filters[targetKey].length ? filters[targetKey] += `, ${value}` : filters[targetKey] = value;
                    }
                    break;

                  default:
                    filters[targetKey] = value;
                }
            }
            ACC.Fetch.getEntities(null, null, filters);
        }
    },
    bindDatePicker: () => {
        const datePickers = document.querySelectorAll('input[type=date].pj-filters');
        for (const datePicker of datePickers) flatpickr(datePicker, {
            altInput: !0,
            altFormat: 'Y-m-d',
            dateFormat: 'Y-m-d',
            minDate: 'today'
        });
    },
    bindTextAlignment: () => {
        const alignmentButtons = document.querySelectorAll('.picklejar-text-alignment');
        if (alignmentButtons.length) for (const alignmentButton of alignmentButtons) alignmentButton.addEventListener('click', event => {
            for (const button of alignmentButtons) button.classList.remove('button-primary');
            const triggerButton = event.target;
            triggerButton.classList.add('button-primary');
            const titleList = document.querySelectorAll('h1.picklejar-title');
            for (const title of titleList) {
                const value = triggerButton.dataset.alignment;
                title.style.textAlign = value, triggerButton.closest('.picklejar-form-group').querySelector('.pj-title-alignment').value = value;
            }
        });
    },
    formatDate: rawDate => {
        const date = new Date(rawDate);
        let month = '' + (date.getMonth() + 1), day = '' + date.getDate(), year = date.getFullYear();
        return month.length < 2 && (month = '0' + month), day.length < 2 && (day = '0' + day), 
        [ year, month, day ].join('-');
    },
    updateContent: (input, target, value, property) => {
        const targetElements = document.querySelectorAll(target);
        targetElements.length && targetElements.forEach(targetElement => {
            if ('innerHTML' === property) targetElement.innerHTML = value; else {
                targetElement.style[property] = value;
            }
        });
    },
    updateInputNumber: (input, type) => {
        const inputValue = input.value;
        let updatedValue = inputValue, minValues = 1;
        switch (document.querySelectorAll(ACC.Swiper.sliderClass).length && (minValues = ACC.Swiper.slidesToShow), 
        type) {
          case 'add':
            updatedValue = parseInt(inputValue) + minValues;
            break;

          case 'minus':
            inputValue - 1 > minValues - 1 && (updatedValue = inputValue - minValues);
            break;

          case 'blur':
            updatedValue = inputValue;
        }
        inputValue !== updatedValue && (input.value = Math.abs(updatedValue), input.dispatchEvent(new Event('change')));
    },
    updateGrid: input => {
        const updateItems = parseInt(input.value), $rowContent = jQuery('#picklejar-layout-example-preview .picklejar-entities-row');
        if ($rowContent.length && updateItems > 0) if ($rowContent.hasClass('picklejar-slider-layout')) {
            const $carouselList = $rowContent.find('.picklejar-slider');
            for (let carousel of $carouselList) {
                const $carousel = jQuery(carousel);
                if ($carousel.hasClass('swiper-initialized')) {
                    let previousItems = $carousel.find('.swiper-slide').length;
                    const swiperSlider = ACC.Swiper.sliders;
                    switch (!0) {
                      case previousItems < updateItems:
                        for (;previousItems < updateItems; ) swiperSlider.appendSlide(ACC.LayoutConfig.eventTemplate), 
                        previousItems++;
                        break;

                      case previousItems > updateItems:
                        for (;previousItems > updateItems; ) previousItems--, swiperSlider.removeSlide(previousItems);
                    }
                    swiperSlider.update();
                }
            }
        } else {
            let newElement = 0;
            for ($rowContent[0].innerHTML = ''; newElement < updateItems; ) $rowContent[0].insertAdjacentHTML('beforeend', ACC.LayoutConfig.eventTemplate), 
            newElement++;
        }
    },
    fetchArtistsAndBandsFilterOptions: () => {
        ACC.LayoutConfig.updateSelectOptions([ 'Artists', 'Bands' ], '.picklejar-filters-artists-band', pj_entities_url);
        const artistBandSelector = document.querySelector('.picklejar-filters-artists-band'), venueSelect = document.querySelector('.picklejar-filters-venue'), selectTriggerArray = [ artistBandSelector ];
        if (venueSelect && selectTriggerArray.push(venueSelect), selectTriggerArray.length) for (const selectTrigger of selectTriggerArray) selectTrigger.addEventListener('change', event => {
            const select = event.target, artistSelect = document.querySelector('.picklejar-filters-artists'), brandSelect = document.querySelector('.picklejar-filters-bands');
            artistSelect.value = null, brandSelect.value = null;
            const selectedIndexOption = select.options.selectedIndex;
            switch (selectedIndexOption >= 1 ? select.options[selectedIndexOption].dataset.entity : '') {
              case 'artists':
                artistSelect.value = select.value, venueSelect && (venueSelect.value = '');
                break;

              case 'bands':
                brandSelect.value = select.value, venueSelect && (venueSelect.value = '');
                break;

              case 'venues':
                brandSelect.value = '', artistSelect.value = '', artistBandSelector.value = '';
                break;

              default:
                brandSelect.value = '', artistSelect.value = '', artistBandSelector.value = '', 
                venueSelect && (artistSelect.value = '');
            }
        });
    },
    fetchVenuesFilterOptions: () => {
        ACC.LayoutConfig.updateSelectOptions([ 'Venues' ], '.picklejar-filters-venue', pj_entities_url);
    },
    fetchCategoriesFilterOptions: () => {
        ACC.LayoutConfig.updateSelectOptions([ '' ], '.pj-filters-category', pj_music_genre);
    },
    updateSelectOptions: (entityList, target, targetUrl) => {
        let completedRequests = 0, optionsHtml = '<option data-entity=""></option>';
        ACC.Fetch.disableActions();
        for (const entity of entityList) {
            const {nonce: nonce, action: action, url: url} = targetUrl, data = {
                nonce: nonce,
                action: action,
                entity: entity
            };
            ACC.Fetch.getData(url, data).then(response => response.json()).then(content => {
                if (!0 === content.success) {
                    const entityLowercase = entity.toLowerCase();
                    completedRequests++;
                    const data = content.output || content.data;
                    if (data) {
                        Object.keys(data).map(id => {
                            data[id].genreName ? optionsHtml += `<option value="${data[id].id}">${data[id].genreName}</option>` : optionsHtml += `<option value="${id}" data-entity="${entityLowercase}">${data[id]}</option>`;
                        });
                        const selectTarget = document.querySelector(`.picklejar-filters-${entityLowercase}`);
                        if (selectTarget && (selectTarget.innerHTML = optionsHtml), completedRequests === entityList.length) {
                            const entityInputSelect = document.querySelectorAll(target);
                            if (entityInputSelect.length) {
                                const selectInput = entityInputSelect[0];
                                selectInput.innerHTML = optionsHtml, selectInput.value = selectInput.dataset.selected || selectInput.dataset.defaultValue, 
                                selectInput.dispatchEvent(new Event('change'));
                                const counter = document.querySelector(`.picklejar-entity-type-${entity}-counter`);
                                counter && (counter.innerHTML = Object.entries(data).length.toString());
                            }
                        }
                    } else {
                        ACC.Dashboard.showWarningNotification({
                            message: 'No results found'
                        });
                        const counter = document.querySelector(`.picklejar-entity-type-${entity}-counter`);
                        counter && (counter.innerHTML = '0');
                    }
                } else ACC.Dashboard.showErrorNotification({
                    message: content.message || content.error.message
                }, 'alert');
            }).finally(() => {
                ACC.Fetch.enableActions(), ACC.Fetch.clearAbortSignal(), ACC.LayoutConfig.triggerEntities++, 
                ACC.LayoutConfig.triggerEntities === ACC.LayoutConfig.totalEntities && document.querySelector('.pj-filters-apply').click();
            });
        }
    },
    saveAndReload: () => {
        const selectorArrays = [ '[name="pj_artists_page[styles][layout]"]', '[name="pj_events_page[styles][layout]"]' ];
        for (const selector of selectorArrays) ACC.LayoutConfig.triggerSaveAndReload(selector);
    },
    triggerSaveAndReload(selector) {
        let layoutOptions = document.querySelectorAll(selector), selectedOption = document.querySelector(`${selector}:checked`);
        if (layoutOptions.length) for (const layout of layoutOptions) layout.addEventListener('change', () => {
            const currentSelectedValue = document.querySelector(`${selector}:checked`).value;
            if (selectedOption.value !== currentSelectedValue) if (confirm('Are you sure you want to update the layout. This action will save and reload the page?')) document.querySelector('.submitbox input[name="save"]').click(); else {
                for (const unselectLayout of layoutOptions) unselectLayout.checked = !1;
                selectedOption.checked = !0;
            }
        });
    }
}, ACC.Login = {
    _autoload: [ [ 'requestOTP', document.querySelectorAll('#generate-login-process').length ] ],
    confirmationToken: '',
    accessToken: '',
    mobilePhone: '',
    loading: !1,
    country: 'US',
    timezone: 'America/New_York',
    language: navigator.language || navigator.userLanguage,
    setLoading: status => {
        const $pjLogin = jQuery('.picklejar-login');
        !0 === status ? $pjLogin.addClass('picklejar-loading') : $pjLogin.removeClass('picklejar-loading');
    },
    focusNextInput: e => {
        const {tabIndex: tabIndex, value: value} = e.target, index = parseInt(tabIndex, 10);
        if (1 === value.length && index < 6) {
            const nextSibling = document.querySelector(`input[tabindex="${index + 1}"]`);
            null !== nextSibling && nextSibling.focus();
        }
        6 === index && ACC.Login.submitOTPForm();
    },
    bindChangeInput: () => {
        jQuery('.number-input').on('change, keydown, keyup', function(e) {
            e.target.value = e.target.value.slice(0, 1), ACC.Login.focusNextInput(e);
        });
    },
    bindChangeEntityType: () => {
        const entityTypeSelectors = document.querySelectorAll('.picklejar-entity-type');
        for (let entityTypeSelector of entityTypeSelectors) entityTypeSelector.addEventListener('change', () => {
            for (let entitySelect of document.querySelectorAll('.pj-select-entity')) entitySelect.classList.add('hidden'), 
            entitySelect.querySelector('select').value = '';
            document.querySelector(`.pj-select-entity-${entityTypeSelector.value}`).classList.remove('hidden');
        });
        const entitySelectInputList = document.querySelectorAll('.pj-select-entity select');
        for (let entitySelect of entitySelectInputList) entitySelect.addEventListener('change', () => {
            document.querySelector('[name="owner_id"]').value = entitySelect.value;
        });
        const $entityTypeSelectorForm = jQuery('#entity-type');
        $entityTypeSelectorForm.length && $entityTypeSelectorForm.validate({
            errorClass: 'picklejar-invalid',
            rules: {
                entity_type: {
                    required: !0
                },
                owner_id: {
                    required: !0
                }
            },
            submitHandler: form => {
                const name = document.querySelector('[name="name"]').value, ownerId = document.querySelector('[name="owner_id"]').value, entityType = document.querySelector('[name="entity_type"]:checked').value;
                let ownerClass = '';
                switch (entityType) {
                  case 'Artists':
                    ownerClass = 'common\\models\\Artist';
                    break;

                  case 'Venues':
                    ownerClass = 'common\\models\\Venue';
                    break;

                  case 'Admin':
                    ownerClass = 'common\\models\\Users';
                }
                if (name && ownerId && ownerClass) {
                    const data = {
                        owner_id: ownerId,
                        owner_class: ownerClass,
                        name: name,
                        origin: window.location.origin
                    };
                    ACC.Login.obtainAuthKey(data, entityType, ownerId);
                } else jQuery(form).valid();
                return !1;
            },
            errorPlacement: function() {
                return !1;
            },
            showErrors: function() {
                this.numberOfInvalids() ? (jQuery('#errorMessageBox span').html('Please select your user'), 
                this.defaultShowErrors()) : jQuery('#errorMessageBox span').html('');
            }
        });
    },
    getCurrentLocation: () => {
        ACC.Login.setLoading(!0), ACC.Fetch.getData('http://ip-api.com/json').then(response => {
            const {timezone: timezone, countryCode: countryCode, status: status} = response;
            if (!status) return !1;
            timezone && (ACC.Login.timezone = timezone), countryCode && (ACC.Login.country = countryCode);
        }).catch(error => {
            console.log('error', error);
        }).finally(() => ACC.Login.setLoading(!1));
    },
    requestOTP: () => {
        ACC.Login.getCurrentLocation();
        const $generateLoginForm = jQuery('#generate-login-process');
        $generateLoginForm.validate({
            errorClass: 'picklejar-invalid',
            rules: {
                phone_number: {
                    required: !0
                },
                country: {
                    required: !0
                },
                timezone: {
                    required: !0
                },
                language: {
                    required: !0
                }
            },
            submitHandler: function() {
                ACC.Login.confirmationToken = '';
                const {action: action, nonce: nonce, url: url} = generate_login_process;
                let serializedForm = ACC.Dashboard.serializeForm($generateLoginForm[0], action, nonce);
                serializedForm.country = ACC.Login.country, serializedForm.timezone = ACC.Login.timezone, 
                serializedForm.language = ACC.Login.language, ACC.Login.mobilePhone = document.getElementById('phone_number').value, 
                ACC.Login.setLoading(!0), ACC.Fetch.postData(url, serializedForm).then(result => {
                    try {
                        const {success: success, data: data} = result;
                        !0 === success ? (ACC.Login.confirmationToken = data.confirmationToken, ACC.Login.loadStep2()) : (ACC.Dashboard.showErrorNotification(result.error), 
                        ACC.Login.setLoading(!1));
                    } catch (err) {
                        ACC.Dashboard.showErrorNotification(err);
                    }
                }).catch(error => {
                    console.log('error', error);
                });
            }
        });
    },
    bindBackToStep1: () => {
        document.getElementById('back-to-step-1').addEventListener('click', () => {
            document.getElementById('picklejar-step-form').innerHTML = '', document.getElementById('step-1').classList.remove('hidden');
        });
    },
    bindOTPValidationSubmitHandler() {
        const $submitForm = jQuery('#otp-validation');
        $submitForm.length && $submitForm.validate({
            errorClass: 'picklejar-invalid',
            rules: {
                number_0: {
                    required: !0
                },
                number_1: {
                    required: !0
                },
                number_2: {
                    required: !0
                },
                number_3: {
                    required: !0
                },
                number_4: {
                    required: !0
                },
                number_5: {
                    required: !0
                }
            },
            submitHandler: function() {
                ACC.Login.submitOTPForm();
            },
            errorPlacement: function() {
                return !1;
            },
            showErrors: function() {
                this.numberOfInvalids() ? (jQuery('#errorMessageBox span').html('Please fill all code inputs'), 
                this.defaultShowErrors()) : jQuery('#errorMessageBox span').html('');
            }
        });
    },
    loadStep2: () => {
        const {action: action, nonce: nonce, url: url} = pj_login_step_2;
        ACC.Fetch.getData(url, {
            action: action,
            nonce: nonce,
            confirmationToken: ACC.Login.confirmationToken,
            mobilePhone: ACC.Login.mobilePhone
        }).then(response => response.json()).then(result => {
            const {content: content, success: success} = result;
            try {
                !0 === success ? (document.getElementById('step-1').classList.add('hidden'), document.getElementById('picklejar-step-form').innerHTML = content, 
                ACC.Login.bindOTPValidationSubmitHandler(), ACC.Login.bindChangeInput(), ACC.Login.bindBackToStep1()) : ACC.Dashboard.showErrorNotification(result.error);
            } catch (err) {
                ACC.Dashboard.showErrorNotification(err);
            }
        }).catch(error => {
            ACC.Dashboard.showErrorNotification(error), console.log('error', error);
        }).finally(() => ACC.Login.setLoading(!1));
    },
    loadStep3: (userId, isHouseAccount) => {
        const {action: action, nonce: nonce, url: url} = pj_login_step_3;
        ACC.Fetch.disableActions(), ACC.Fetch.getData(url, {
            action: action,
            nonce: nonce,
            continue: !!isHouseAccount
        }).then(response => response.json()).then(result => {
            const {content: content, success: success} = result;
            try {
                if (!0 === success) if (document.getElementById('picklejar-step-form').innerHTML = content, 
                isHouseAccount) {
                    const name = document.querySelector('[name="name"]').value;
                    if (name) {
                        const data = {
                            owner_id: userId,
                            owner_class: 'common\\models\\User',
                            name: name,
                            origin: window.location.origin
                        };
                        ACC.Login.obtainAuthKey(data, 'Admin', null);
                    }
                } else ACC.Login.bindChangeEntityType(), ACC.LayoutConfig.updateSelectOptions([ 'Artists' ], '.pj-select-Artists', pj_entities_url), 
                ACC.LayoutConfig.updateSelectOptions([ 'Venues' ], '.pj-select-Venues', pj_entities_url); else ACC.Dashboard.showErrorNotification(result.error);
            } catch (err) {
                ACC.Dashboard.showErrorNotification(err);
            }
        }).catch(error => {
            ACC.Dashboard.showErrorNotification(error), console.log('error', error);
        }).finally(() => ACC.Login.setLoading(!1));
    },
    loadStep4: (authKey, entityType, entityId) => {
        const {action: action, nonce: nonce, url: url} = pj_login_step_4, data = {
            action: action,
            nonce: nonce
        };
        authKey && (data.authKey = authKey), entityType && (data.entityType = entityType), 
        entityId && (data.entityValue = entityId), ACC.Fetch.disableActions(), ACC.Fetch.getData(url, data, {
            'Content-Type': 'text/html'
        }).then(response => response.text()).then(content => {
            document.getElementById('picklejar-step-form').innerHTML = content, document.getElementById('picklejar_integration_plugin[settings][pj_validation_token]').value = authKey, 
            document.getElementById('picklejar_integration_plugin[settings][pj_entity_type]').value = entityType, 
            document.getElementById('picklejar_integration_plugin[settings][pj_entity_id]').value = entityId, 
            authKey && ('Admin' === entityType && (document.getElementById('picklejar_integration_plugin[settings][pj_entity_value]').value = 1), 
            setTimeout(() => {
                document.querySelector('#save-access-token input[type="submit"]').click();
            }, 1500));
        }).catch(error => {
            ACC.Dashboard.showErrorNotification(error), console.log('error', error);
        }).finally(() => {
            ACC.Login.setLoading(!1), ACC.Fetch.enableActions();
        });
    },
    submitOTPForm() {
        if (jQuery('#otp-validation-submit').valid()) {
            const formData = jQuery('#otp-validation').serializeArray().map(object => object.value);
            setTimeout(() => ACC.Login.handleOTPFormSubmit(formData).then(result => {
                try {
                    const {success: success, data: data, error: error} = result;
                    !0 === success ? (ACC.Login.accessToken = data.token.accessToken, ACC.Login.loadStep3(data.id, data.isHouseAccount)) : ACC.Dashboard.showErrorNotification(error);
                } catch (err) {
                    ACC.Dashboard.showErrorNotification(err);
                }
            }).catch(error => {
                ACC.Dashboard.showErrorNotification(error), console.log('error', error);
            }).finally(() => ACC.Login.setLoading(!1)), 0);
        }
    },
    handleOTPFormSubmit: async formData => {
        formData && ACC.Login.confirmationToken || alert('missing data');
        const confirmationCode = Object.values(formData).join(''), {action: action, nonce: nonce, url: url} = pj_new_login, data = `action=${action}&nonce=${nonce}&confirmation_numbers=${confirmationCode}&confirmation_token=${ACC.Login.confirmationToken}`;
        return ACC.Login.setLoading(!0), jQuery.ajax({
            type: 'post',
            url: url,
            data: data
        });
    },
    loadUserEntities: entity => {
        ACC.LayoutConfig.updateSelectOptions([ entity ], '.pj-select-user', pj_entities_url);
    },
    obtainAuthKey: (data, entityType, entityId) => {
        const {action: action, nonce: nonce, url: url} = pj_create_auth_login;
        data.action = action, data.nonce = nonce, ACC.Fetch.disableActions(), ACC.Fetch.postData(url, data).then(result => {
            const {success: success, message: message, output: output} = result;
            try {
                !0 === success ? 'Created' === message && ACC.Login.loadStep4(output.auth_key, entityType, entityId) : ACC.Dashboard.showErrorNotification(result.message);
            } catch (err) {
                ACC.Dashboard.showErrorNotification(err);
            }
        }).catch(error => {
            ACC.Dashboard.showErrorNotification(error), console.log('error', error);
        }).finally(() => {
            ACC.Login.setLoading(!1), ACC.Fetch.enableActions();
        });
    }
}, ACC.Messages = {
    _autoload: [],
    noEventsFoundMessage: '<h3 class="picklejar-message">No Events Found</h3>',
    noArtistsFoundMessage: '<h3 class="picklejar-message">No Artists Found</h3>'
}, ACC.Render = {
    _autoload: [],
    renderEntityModel: data => ({
        id: data.id,
        categories: data.categories,
        name: data.name,
        message: data.message,
        isVerified: data.isVerified,
        avatar: data.avatar,
        genre: data.genre,
        banner: data.banner,
        tipButtonId: data.tipButtonId,
        tipButton: data.tipButton,
        socialLinks: data.socialLinks
    }),
    renderGridTemplate: (eventData, eventStyleParameters) => {
        let eventContent = '';
        for (const event of eventData) {
            const eventData = event;
            Object.assign(event, eventStyleParameters), eventContent += '<div class="picklejar-entity picklejar-col-12 picklejar-col-md-6 picklejar-col-xl-4">', 
            eventContent += '<div class="picklejar-entity-column">', eventContent += ACC.Render.renderEntities(eventData), 
            eventContent += '</div>', eventContent += '</div>';
        }
        return eventContent;
    },
    renderSliderTemplate: (eventData, eventStyleParameters) => {
        let eventContent = '';
        return ACC.InfiniteScroll.loadMore ? eventContent += ACC.Render.renderSlideTemplate(eventData, eventStyleParameters) : (eventContent += `<div class="swiper-button-prev picklejar-swiper-control" ${eventStyleParameters.sliderControlColor}></div>`, 
        eventContent += '<div class="swiper picklejar-slider">', eventContent += '<div class="swiper-wrapper">', 
        eventContent += ACC.Render.renderSlideTemplate(eventData, eventStyleParameters), 
        eventContent += '</div>', eventContent += '</div>', eventContent += `<div class="swiper-button-next picklejar-swiper-control" ${eventStyleParameters.sliderControlColor}></div>`), 
        eventContent;
    },
    renderSlideTemplate: (eventData, eventStyleParameters) => {
        let eventContent = '';
        for (const event of eventData) {
            const eventData = event;
            Object.assign(event, eventStyleParameters), eventContent += '<div class="swiper-slide">', 
            eventContent += '<div class="picklejar-entity-column">', eventContent += ACC.Render.renderEntities(eventData), 
            eventContent += '</div>', eventContent += '</div>';
        }
        return eventContent;
    },
    renderDates: (colorDate, dateDay, dateMonth, dateDate, dateTime, dateTimeZone) => `<div class="picklejar-event-date" style="${colorDate}">\n        <div class="picklejar-day">${dateDay}</div>\n        <div class="picklejar-month">\n          <div class="picklejar-divider">${dateMonth}</div>\n          <div class="picklejar-divider">${dateDate}</div>\n        </div>\n        <div class="picklejar-hour">\n          <div class="picklejar-divider"> ${dateTime}</div>\n          <div class="picklejar-divider">${dateTimeZone}</div>\n        </div>\n      </div>`,
    renderEntities: entity => {
        let entityData = entity, detailUrl = ACC.Dashboard.entityDetailsPage;
        entity.artist && (entityData = Object.assign(entity, entity.artist));
        let {avatar: avatar, address: address, banner: banner, eventAdmins: eventAdmins, eventDates: eventDates, handle: handle, avatarProfileTextColor: avatarProfileTextColor, avatarProfileBorderColor: avatarProfileBorderColor, colorDate: colorDate, locationTextColor: locationTextColor} = entityData, formattedData = address && address.formatted_address ? address.formatted_address : '', bannerImage = banner && banner.mediumImage ? `style="background-image: url(${banner.mediumImage}"` : '', avatarImage = avatar.smallImage ? avatar.smallImage : '', startDate = '', endDate = '', startDateDay = '', startDateDate = '', startDateMonth = '', startDateTime = '', startDateTimeZone = '', endDateDate = '', endDateDay = '', endDateMonth = '', endDateTime = '', endDateTimeZone = '';
        const totalEvents = eventDates ? eventDates.length : 0, isPreview = document.getElementById('picklejar-layout-example-preview');
        if (eventAdmins && eventAdmins.length && eventAdmins[0].artist && (handle = eventAdmins[0].artist.handle), 
        totalEvents) for (let [eventIndex, currentEvent] of eventDates.entries()) 0 === eventIndex && currentEvent.formattedDateTimeStart && ((startDate = currentEvent.formattedDateTimeStart).date && (startDateDate = startDate.date), 
        startDate.day && (startDateDay = startDate.day), startDate.month && (startDateMonth = startDate.month), 
        startDate.time && (startDateTime = startDate.time), startDate.timeZone && (startDateTimeZone = startDate.timeZone)), 
        eventIndex + 1 === totalEvents && currentEvent.formattedDateTimeEnd && ((endDate = currentEvent.formattedDateTimeEnd).date && (endDateDate = endDate.date), 
        endDate.day && (endDateDay = endDate.day), endDate.month && (endDateMonth = endDate.month), 
        endDate.time && (endDateTime = endDate.time), endDate.timeZone && (endDateTimeZone = endDate.timeZone));
        let eventContent = '';
        eventContent += isPreview ? `<div class="picklejar-event-card" ${bannerImage}>` : `<a href="${detailUrl || '#'}?entityId=${entityData.id}&nonce=${ACC.EntityDetails.nonce}" class="picklejar-event-card" ${bannerImage}>`;
        let eventStartDate = '';
        startDateDay && (eventStartDate = ACC.Render.renderDates(colorDate, startDateDay, startDateMonth, startDateDate, startDateTime, startDateTimeZone));
        let eventEndDate = '';
        return eventEndDate && (eventEndDate = ACC.Render.renderDates(colorDate, endDateDay, endDateMonth, endDateDate, endDateTime, endDateTimeZone)), 
        eventContent += `\n        <div class="picklejar-event-card-header">\n          ${eventStartDate}\n          ${eventEndDate}\n        </div>\n        <div class="picklejar-event-card-footer">\n          <div class="picklejar-avatar" style="${avatarProfileBorderColor}">\n            <img\n              class="picklejar-avatar-image"\n              src="${avatarImage || ACC.Dashboard.defaultImage}"\n              alt="avatar-image"\n            />\n          </div>\n          <div class="d-flex picklejar-d-column picklejar-contact-info">\n            <div class="picklejar-contact"  style="${avatarProfileTextColor}">\n              ${handle}\n            </div>\n            <small class="picklejar-location" style="${locationTextColor}">\n              ${formattedData}\n            </small>\n          </div>\n        </div>`, 
        eventContent += isPreview ? '</div>' : '</a>';
    }
}, ACC.Swiper = {
    _autoload: [ [ 'initPicklejarSlider', document.querySelectorAll('#picklejar-layout-example-preview').length ] ],
    sliders: [],
    sliderClass: '.picklejar-slider',
    slidesToShow: 1,
    loop: !1,
    picklejarSliderConfig: {
        spaceBetween: 10,
        breakpointsBase: 'container',
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 20
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 40
            }
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev'
        },
        on: {
            init: function(a) {
                jQuery(a.el).closest('.picklejar-item-row').find('.picklejar-swiper-control').addClass('swiper-initialized');
            }
        }
    },
    initPicklejarSlider: config => {
        if (ACC.Swiper.picklejarSliderConfig.slidesPerView = ACC.Swiper.slidesToShow, config && Object.keys(config).forEach(key => ACC.Swiper.picklejarSliderConfig[key] = config[key]), 
        document.getElementById('picklejar-layout-example-preview')) document.getElementById('picklejar-preview') && (ACC.Swiper.picklejarSliderConfig.breakpointsBase = 'window'), 
        document.querySelectorAll('.swiper-slide').length && (ACC.Swiper.sliders = new Swiper('#picklejar-layout-example-preview ' + ACC.Swiper.sliderClass, ACC.Swiper.picklejarSliderConfig)); else if (!document.getElementById('wpbody-content')) {
            ACC.Swiper.picklejarSliderConfig.breakpointsBase = 'container';
            const getSliders = document.querySelectorAll(`${ACC.Swiper.sliderClass}:not([data-pj-slider-id])`);
            for (let actualSlider of getSliders) {
                const randomId = 'picklejar-swiper-' + ACC.Dashboard.randomId();
                actualSlider.setAttribute('data-pj-slider-id', randomId);
                const slider = new Swiper(`[data-pj-slider-id="${randomId}"]`, ACC.Swiper.picklejarSliderConfig);
                ACC.Swiper.sliders[randomId] = slider, slider && slider.on('reachEnd', swiper => {
                    slider.allowSlideNext = !1, slider.allowTouchMove = !1;
                    const currentSwiperElement = swiper.el;
                    ACC.Events.requestSliderId = currentSwiperElement.dataset.pjSliderId;
                    const eventRow = currentSwiperElement.closest('.picklejar-entities-row');
                    ACC.InfiniteScroll.loadMore = !0, ACC.Events.getEvents(eventRow, null, null);
                });
            }
        }
    },
    destroyPicklejarSlider: () => {
        jQuery(ACC.Swiper.sliderClass).swiper('destroy');
    }
}, ACC.Throttle = {
    _autoload: [],
    timerId: null,
    defaultThrottleTime: 500,
    throttleFunction: (func, delay, target) => {
        if (ACC.Throttle.timerId) return;
        const targetEventElement = document.getElementById(target.getAttribute('data-pj-entity-target'));
        ACC.Throttle.timerId = setTimeout(() => {
            func(targetEventElement, null, null), ACC.Throttle.timerId = void 0;
        }, delay);
    }
}, ACC.Utils = {
    _autoload: [],
    generateUnixDate(convertDate) {
        const date = convertDate ? new Date(convertDate) : new Date();
        return Math.floor(date.getTime() / 1e3);
    },
    getDataset: (element, pageSize, filters) => {
        const dataSet = element.dataset.pjLayoutConfig, searchTermsInput = document.querySelector('.picklejar-filter-search-term-input');
        let elementFilters = filters, eventPageSize = pageSize, currentElementParameters = {}, eventStyles = {};
        if (dataSet) try {
            eventStyles = (currentElementParameters = JSON.parse(dataSet)).styles;
            const elementFilterList = currentElementParameters.filters;
            if (!filters && elementFilterList) try {
                elementFilters = JSON.parse(elementFilterList);
            } catch (error) {
                console.log('error', error);
            }
            if (!elementFilters) return ACC.Fetch.queryEntities(element, eventPageSize, elementFilters, eventStyles);
            if (ACC.InfiniteScroll.activeScrollId.push(element.id), !elementFilters.totalPages || elementFilters.page <= parseInt(elementFilters.totalPages)) {
                if (elementFilters.page > elementFilters.totalPages) {
                    const targetLoaderId = element.getAttribute('pj-loader-indicator'), targetLoaderEvent = document.querySelector(targetLoaderId);
                    targetLoaderEvent.removeEventListener('scroll', ACC.InfiniteScroll.handleInfiniteScroll), 
                    targetLoaderEvent.remove();
                }
                return pageSize || (eventPageSize = 12), elementFilters || (elementFilters = {}), 
                searchTermsInput && !elementFilters.search_term && (elementFilters.search_term = searchTermsInput.value.trim()), 
                ACC.Fetch.queryEntities(element, eventPageSize, elementFilters, eventStyles);
            }
        } catch (error) {
            console.log('error', error);
        }
    },
    updateDataFilters(currentElement, formattedResponse, elementFilters) {
        const currentElementParameters = currentElement.dataset.pjLayoutConfig, currentElementFormatted = JSON.parse(currentElementParameters);
        let currentElementFilters = {};
        if (currentElementFormatted.filters && (currentElementFilters = JSON.parse(currentElementFormatted.filters)), 
        elementFilters) for (const filter of Object.keys(elementFilters)) currentElementFilters[filter] = elementFilters[filter];
        currentElementFilters.page = parseInt(formattedResponse.currentPage) + 1, currentElementFilters.totalPages = formattedResponse.totalPages, 
        currentElementFormatted.filters = JSON.stringify(currentElementFilters), currentElement.dataset.pjLayoutConfig = JSON.stringify(currentElementFormatted);
    }
};

const _autoload = () => {
    Object.values(ACC).forEach((obj, index) => {
        Object.values(obj._autoload).forEach(value => {
            let section = Object.keys(ACC)[index];
            Array.isArray(value) ? value[1] ? ACC[section][value[0]]() : value[2] && ACC[section][value[2]]() : ACC[section][value]();
        });
    });
};

document.addEventListener('DOMContentLoaded', () => {
    Object.values(ACC).forEach((obj, index) => {
        Object.values(obj._autoload).forEach(value => {
            let section = Object.keys(ACC)[index];
            Array.isArray(value) ? value[1] ? ACC[section][value[0]]() : value[2] && ACC[section][value[2]]() : ACC[section][value]();
        });
    });
}, !1);