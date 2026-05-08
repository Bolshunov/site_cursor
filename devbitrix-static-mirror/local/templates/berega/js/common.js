(function () {

const cardsRoot = document.getElementById('growthCards');
const filterButtons = Array.from(document.querySelectorAll('.growth-filter'));
const showMoreButton = document.getElementById('growthShowMore');
const INITIAL_VISIBLE_CARDS = 6;

function initGrowthCases() {
  if (!cardsRoot) return;

  // Вместо генерации — просто находим все карточки, которые уже есть в HTML
  const allCards = Array.from(cardsRoot.querySelectorAll('.growth-card'));
  let activeFilter = 'all';
  let expanded = false;

  const applyCardsVisibility = () => {
    // Фильтруем карточки по data-category, который должен быть в HTML
    const filteredCards = allCards.filter((card) => 
      activeFilter === 'all' || card.dataset.category === activeFilter
    );

    // Скрываем вообще все
    allCards.forEach((card) => { card.style.display = 'none'; });

    // Показываем только нужные по фильтру и лимиту "Показать еще"
    filteredCards.forEach((card, index) => {
      const shouldShow = expanded || index < INITIAL_VISIBLE_CARDS;
      card.style.display = shouldShow ? '' : 'none';
    });

    if (showMoreButton) {
      const needShowMore = filteredCards.length > INITIAL_VISIBLE_CARDS && !expanded;
      showMoreButton.style.display = needShowMore ? 'inline-flex' : 'none';
    }
  };

  filterButtons.forEach((button) => {
    button.addEventListener('click', () => {
      activeFilter = button.dataset.filter || 'all';
      expanded = false;

      filterButtons.forEach((btn) => btn.classList.remove('is-active'));
      button.classList.add('is-active');

      applyCardsVisibility();
    });
  });

  if (showMoreButton) {
    showMoreButton.addEventListener('click', () => {
      expanded = true;
      applyCardsVisibility();
    });
  }

  // Запускаем первичную настройку видимости
  applyCardsVisibility();
}

initGrowthCases();

  const budgetWraps = Array.from(document.querySelectorAll('.scenario-budget-wrap'));
  budgetWraps.forEach((budgetWrap) => {
    const budgetToggle = budgetWrap.querySelector('.scenario-budget');
    const budgetLabel = budgetWrap.querySelector('.scenario-budget__label');
    const budgetHidden = budgetWrap.querySelector('input[type="hidden"]');
    const budgetOptions = Array.from(budgetWrap.querySelectorAll('.scenario-budget-option'));

    const closeBudgetMenu = () => {
      budgetWrap.classList.remove('is-open');
      if (budgetToggle) budgetToggle.setAttribute('aria-expanded', 'false');
    };

    if (budgetToggle) {
      budgetToggle.addEventListener('click', () => {
        const isOpen = budgetWrap.classList.toggle('is-open');
        budgetToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      });
    }

    budgetOptions.forEach(option => {
      option.addEventListener('click', () => {
        const value = option.dataset.value || '';
        if (budgetLabel) budgetLabel.textContent = option.textContent || 'Бюджет';
        if (budgetHidden) budgetHidden.value = value;
        closeBudgetMenu();
      });
    });

    document.addEventListener('click', (event) => {
      if (!budgetWrap.contains(event.target)) closeBudgetMenu();
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') closeBudgetMenu();
    });
  });

  const slides = Array.from(document.querySelectorAll('.construction-main__slide'));
  if (slides.length > 1) {
    let slideIndex = 0;
    setInterval(() => {
      slides[slideIndex].classList.remove('is-active');
      slideIndex = (slideIndex + 1) % slides.length;
      slides[slideIndex].classList.add('is-active');
    }, 2000);
  }

  const podcastBtn = document.getElementById('podcastOpen');
  const constructionModal = document.getElementById('constructionModal');
  const constructionModalClose = document.getElementById('constructionModalClose');
  const constructionModalFrame = document.getElementById('constructionModalFrame');
  const rutubeVideoUrl = 'https://rutube.ru/video/4e4703733ddb245dcf05847a6d757570/';

  const toRutubeEmbed = (url) => {
    const match = url.match(/rutube\.ru\/video\/([a-zA-Z0-9]+)/);
    if (!match) return '';
    return `https://rutube.ru/play/embed/${match[1]}`;
  };

  const openConstructionModal = () => {
    if (!constructionModal || !constructionModalFrame) return;
    const embed = toRutubeEmbed(rutubeVideoUrl);
    if (!embed) return;
    constructionModalFrame.src = `${embed}?autoplay=1`;
    constructionModal.classList.add('is-open');
    constructionModal.setAttribute('aria-hidden', 'false');
  };

  const closeConstructionModal = () => {
    if (!constructionModal || !constructionModalFrame) return;
    constructionModal.classList.remove('is-open');
    constructionModal.setAttribute('aria-hidden', 'true');
    constructionModalFrame.src = '';
  };

  if (podcastBtn) podcastBtn.addEventListener('click', openConstructionModal);
  if (constructionModalClose) constructionModalClose.addEventListener('click', closeConstructionModal);
  if (constructionModal) {
    constructionModal.addEventListener('click', (event) => {
      if (event.target === constructionModal) closeConstructionModal();
    });
  }

  const videoCard = document.querySelector('.founder-video');
  const playBtn = videoCard ? videoCard.querySelector('.founder-video__play') : null;
  if (videoCard && playBtn) {
    playBtn.addEventListener('click', () => {
      const embedUrl = videoCard.dataset.rutube || '';
      if (!embedUrl || embedUrl.includes('REPLACE_ME')) return;
      videoCard.innerHTML = `<iframe class="founder-video__frame" src="${embedUrl}?autoplay=1" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>`;
    });
  }

  const contentHubTabs = Array.from(document.querySelectorAll('.content-hub-tab'));
  if (contentHubTabs.length) {
	  /*
    const hubTitles = Array.from(document.querySelectorAll('.content-hub-copy__title'));
    const hubDurations = Array.from(document.querySelectorAll('.content-hub-meta__row span:last-child'));
    const hubPrimaryTag = document.querySelector('.content-hub-card--large .content-hub-tag');

    const hubConfig = {
      video: {
        leadTitle: 'Из видео вы узнаете:',
        duration: '5 мин. просмотра',
        primaryTag: 'Маркетинг'
      },
      article: {
        leadTitle: 'Из статьи вы узнаете:',
        duration: '5 мин. чтения',
        primaryTag: 'Аналитика'
      },
      podcast: {
        leadTitle: 'Из подкаста вы узнаете:',
        duration: '5 мин. прослушивания',
        primaryTag: 'Обзор'
      }
    };

    const renderHubByTab = (tabName) => {
      const data = hubConfig[tabName] || hubConfig.video;
      hubTitles.forEach(el => { el.textContent = data.leadTitle; });
      hubDurations.forEach(el => { el.textContent = data.duration; });
      if (hubPrimaryTag) hubPrimaryTag.textContent = data.primaryTag;
    };

    contentHubTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        contentHubTabs.forEach(btn => {
          btn.classList.remove('is-active');
          btn.setAttribute('aria-selected', 'false');
        });
        tab.classList.add('is-active');
        tab.setAttribute('aria-selected', 'true');
        renderHubByTab(tab.dataset.tab || 'video');
      });
    });

    renderHubByTab('video');
	*/
  }

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && constructionModal && constructionModal.classList.contains('is-open')) {
      closeConstructionModal();
    }
  });


  /* Keyboard navigation for content hub tabs */
/*
  const tabList = document.querySelector('.content-hub-tabs');
  if (tabList) {
    tabList.addEventListener('keydown', (e) => {
      const tabs = Array.from(tabList.querySelectorAll('.content-hub-tab'));
      const current = tabs.findIndex(t => t.classList.contains('is-active'));
      let next = current;

      if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
        e.preventDefault();
        next = (current + 1) % tabs.length;
      } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
        e.preventDefault();
        next = (current - 1 + tabs.length) % tabs.length;
      } else if (e.key === 'Home') {
        e.preventDefault();
        next = 0;
      } else if (e.key === 'End') {
        e.preventDefault();
        next = tabs.length - 1;
      }

      if (next !== current) {
        tabs[next].click();
        tabs[next].focus();
      }
    });
  }
*/

  /* Disable map scroll zoom (if Yandex Maps present) */
  if (typeof ymaps !== 'undefined') {
    ymaps.ready(function () {
      var maps = document.querySelectorAll('.ya-map, [id*="yandex"], [class*="ymaps"]');
      maps.forEach(function (el) {
        if (el._map) el._map.behaviors.disable('scrollZoom');
      });
    });
  }



  /* Fancybox defaults */
  if (typeof jQuery !== 'undefined' && jQuery.fancybox) {
    jQuery.fancybox.defaults.btnTpl = jQuery.fancybox.defaults.btnTpl || {};
  }
})();

$(document).ready(function () {

  $(".btn-menu").click(function () {
		if ($(".hero-nav").is(":hidden")) {
			$(".hero-nav").slideDown(200);
			$(".btn-menu").addClass("active");
			$("body").addClass("no-scroll");
			$(".menu-overlay").fadeIn(200);
		} else {
			$(".hero-nav").slideUp(200);
			$(".btn-menu").removeClass("active");
			$("body").removeClass("no-scroll");
			$(".menu-overlay").fadeOut(200);
		}
	});

    $(".menu-overlay, .hero-nav__close").click(function () {
			$(".hero-nav").slideUp(200);
			$(".btn-menu").removeClass("active");
			$("body").removeClass("no-scroll");
			$(".menu-overlay").fadeOut(200);
	});

	$('.slider-four').slick({
		arrows: true,
		dots: false,
		infinite: true,
		touchThreshold: 1000,
		slidesToShow: 4,
		slidesToScroll: 1,
		prevArrow: '<div class="slick-prev slick-arrow"><i class="far fa-arrow-left"></i><div/>',
		nextArrow: '<div class="slick-next slick-arrow"><i class="far fa-arrow-right"></i><div/>',
		responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 1,
          variableWidth: true,
          centerMode: true,
					arrows: false,
					dots: false,
				}
			}
		]
	});

  	$('.slider-cases').slick({
		arrows: true,
		dots: false,
		infinite: true,
		touchThreshold: 1000,
		slidesToShow: 1,
		slidesToScroll: 1,
		prevArrow: '<div class="slick-prev slick-arrow"><i class="far fa-arrow-left"></i><div/>',
		nextArrow: '<div class="slick-next slick-arrow"><i class="far fa-arrow-right"></i><div/>',
		responsive: [
			{
				breakpoint: 992,
				settings: {
					slidesToShow: 1,
          variableWidth: true,
					arrows: false,
					dots: true,
				}
			}
		]
	});

  	//Попап менеджер FancyBox
	$(".fancybox").fancybox({
		autoFocus: false,
		backFocus: false,
	});

  //hint
  	$('.hint').each(function () {
		const text = $(this).attr('data-hint');
		if (text && $(this).find('.custom-tooltip').length === 0) {
			$(this).append('<div class="custom-tooltip">' + text + '</div>');
		}
	});

	$('.hint').on('click', function (e) {
		if ($(window).width() < 992) {
			e.stopPropagation();

			const $tooltip = $(this).find('.custom-tooltip');

			$('.custom-tooltip').not($tooltip).removeClass('is-active');

			$tooltip.toggleClass('is-active');
		}
	});

	$(document).on('click', function () {
		$('.custom-tooltip').removeClass('is-active');
	});

  	{
		if ($(window).width() < 992) {
			//footer
			$(".site-footer__col-title").click(function () {
				$(this).toggleClass("active");
				$(this).next(".footer__content").slideToggle(200);
			});


		}
	}

  //button more cards
    const $container = $('.growth-cards');
    const cardsCount = $container.find('.growth-card').length;

    if (cardsCount > 6) {
        $('.growth-more-wrap').addClass('active');
    }


	$(".input-phone").mask("+7 (999) 999-99-99");

  
//list nav
setTimeout(() => {
      $('.nav-article.show-mobile ul').each(function () {
        const $list = $(this);
        const $bottomBlock = $(this).siblings(".nav-article__button");
        const $items = $list.children('li');
        const limit = 2;

        if ($items.length > limit) {

            $items.slice(limit).hide();

            const $btn = $('<div class="link-arrow"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_1163_2674)"><path d="M11.9998 15L7.75684 10.757L9.17184 9.34302L11.9998 12.172L14.8278 9.34302L16.2428 10.757L11.9998 15Z" fill="black" /></g><defs><clipPath id="clip0_1163_2674"><rect width="24" height="24" fill="white" /></clipPath></defs></svg></div>');

            $bottomBlock.append($btn);

            $btn.on('click', function () {
                if ($btn.hasClass('is-open')) {
                    $items.slice(limit).slideUp(200);
                    $btn.removeClass('is-open');
                } else {
                    $items.slice(limit).slideDown(200);
                     $btn.addClass('is-open');
                }
            });
        }
    });
}, 200);

  });