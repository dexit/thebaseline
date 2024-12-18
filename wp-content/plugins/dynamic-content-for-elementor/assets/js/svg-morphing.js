( function( $ ) {

	var WidgetElements_SvgMorphHandler = function( $scope, $ ) {
		var elementSettings = dceGetElementSettings($scope);
		var id_scope = $scope.attr('data-id');
		var forma = elementSettings.type_of_shape;
		var playpause_control = elementSettings.playpause_control || 'paused';
		var step = 0;
		var run = $('#dce-svg-'+id_scope).attr('data-run');
		var is_running = false;
		var trigger_svg = elementSettings.svg_trigger;
		var one_by_one = elementSettings.one_by_one;
		var enable_image = elementSettings.enable_image || 0;
		var pattern_image = '';

		if (enable_image) {
			pattern_image = elementSettings.svg_image.id;
		}

		// ciclo il ripetitore in base alla Forma
		var ripetitore = 'repeater_shape_'+forma;
		eval('var repeaterShape = elementSettings.'+ripetitore);
		var contentElemsTotal = repeaterShape.length;
		var numberOfElements = repeaterShape.length;
		var shapes = [];
		var dceshape = "#shape-"+id_scope;
		var dceshape_svg = "#dce-svg-"+id_scope;

		if(tl) {
			tl.kill($(dceshape));
		}
		var tl = null;
		tl = new gsap.timeline();

		if(tlpos) {
			tlpos.kill($(dceshape_svg));
		}
		var tlpos = null;
		tlpos = new gsap.timeline();

		var transitionImgAll = new gsap.timeline();
		var transitionImg = new gsap.timeline();

		var dceshape_delay = elementSettings.duration_morph.size || 2,
		dceshape_speed = elementSettings.speed_morph.size || 1;

		var easing_morph_ease = elementSettings.easing_morph_ease || 'Power3',
		easing_morph = elementSettings.easing_morph || 'easeInOut';

		var repeat_morph = elementSettings.repeat_morph;

		if(transitionTl) transitionTl.kill($(dceshape));
		var transitionTl = null;

		if(transitionTl) transitionTlpos.kill($(dceshape_svg));
		var transitionTlpos = null;

		var get_data_anim = function(){
			var duration_anim = elementSettings.duration_morph.size || 3;
			var speed_anim = elementSettings.speed_morph.size || 1;

			easing_morph_ease = elementSettings.easing_morph_ease;
			easing_morph = elementSettings.easing_morph;

			repeat_morph = elementSettings.repeat_morph;

			dceshape_delay = duration_anim;
			dceshape_speed = speed_anim;
		};
		var get_data_shape = function(){
			shapes = [];

			var ciccio = [];
			if( elementorFrontend.isEditMode()){
				ciccio = repeaterShape.models;
			}else{
				ciccio = repeaterShape;
			}
			var old_points = '';
			$.each(ciccio, function(i, el){
				var pippo = [];
				if( elementorFrontend.isEditMode()){
					pippo = repeaterShape.models[i].attributes;
				}else{
					pippo = repeaterShape[i];
				}

				var id_shape = pippo.id_shape;
				var points = pippo.shape_numbers;
				if(points == ''){
					points = old_points;
				}
				old_points = points;


				var fillColor = pippo.fill_color;
				var strokeColor = pippo.stroke_color;
				var strokeWidth = pippo.stroke_width.size || 0;
				var shapeX = pippo.shape_x.size || 0;
				var shapeY = pippo.shape_y.size || 0;
				var shapeRotate = pippo.shape_rotation.size || 0;

				var dceshape_delay = elementSettings.duration_morph.size || 2,
				dceshape_speed = elementSettings.speed_morph.size || 1;

				var objRep = {
					points: points,
					path: {
						duration: pippo.duration_morph.size,
						speed: pippo.speed_morph.size,
						easing: pippo.easing_morph_ease,
						morph: pippo.easing_morph,
						elasticity: 600,
					},
					fill: {
						color: fillColor,
						image: pippo.fill_image.id
					},
					stroke: {
						width: strokeWidth,
						color: strokeColor
					},
					svg: {
						x: shapeX,
						y: shapeY,
						rotate: shapeRotate,
						elasticity: 600

					}
				};
				shapes.push(objRep);
			});

		};
		var getCustomData_speed = function(i){
			if( shapes[i].path.speed ){
				dceshape_speed = shapes[i].path.speed;
			}else{
				dceshape_speed = elementSettings.speed_morph.size;
			}
			return dceshape_speed;
		};
		var getCustomData_duration = function(i){
			if( shapes[i].path.duration ){
				dceshape_delay = shapes[i].path.duration;
			}else{
				dceshape_delay = elementSettings.duration_morph.size;
			}
			return dceshape_delay;
		};
		var getCustomData_easing = function(i){

			if( shapes[i].path.easing ){
				easing_morph_ease = shapes[i].path.easing;
			}else{
				easing_morph_ease = elementSettings.easing_morph_ease;
			}
			return easing_morph_ease;
		};
		var getCustomData_morph = function(i){
			if( shapes[i].path.morph ){
				easing_morph = shapes[i].path.morph;
			}else{
				easing_morph = elementSettings.easing_morph;
			}
			return easing_morph;
		};
		var getCustomData_image = function(i){
			if( shapes[i].fill.image ){
				easing_morph = shapes[i].fill.image;
			}else{
				easing_morph = elementSettings.easing_morph;
			}
			return easing_morph;
		};
		var createTween = function(){
			if($("#shape-"+id_scope).length){

				var tweenSVG = 'tlpos';
				var tweenString = 'tl';

				$.each(shapes, function(i, el){

						var fill_element = 'fill:"'+shapes[i].fill.color+'", ';
						if(enable_image && (shapes[i].fill.image || pattern_image)){
							fill_element = ''; //'fill: url(#pattern-'+id_scope+')';
							$(dceshape).attr('fill','url(#pattern-'+id_scope+')');
						}
						if(i > 0){
							tweenString += '.to("'+dceshape+'", '+getCustomData_speed(i)+', {onStart: moveFnStart, onStartParams:['+i+'], onComplete: myFunction1, onCompleteParams:['+i+'], morphSVG:`'+shapes[i].points+'`, ease: '+getCustomData_easing(i)+'.'+getCustomData_morph(i)+', attr:{'+fill_element+'"stroke-width":'+shapes[i].stroke.width+', stroke:"'+shapes[i].stroke.color+'"}}, "+='+getCustomData_duration(i)+'")';
							tweenSVG += '.to("'+dceshape_svg+'", '+getCustomData_speed(i)+', {rotation:'+shapes[i].svg.rotate+', x:'+shapes[i].svg.x+', y:'+shapes[i].svg.y+', ease: '+getCustomData_easing(i)+'.'+getCustomData_morph(i)+'}, "+='+getCustomData_duration(i)+'")';
						}
				});
				var fill_element = 'fill:"'+shapes[0].fill.color+'", ';
				if(enable_image && (shapes[0].fill.image || pattern_image)){
					fill_element = ''; //'fill: url(#pattern-'+id_scope+')';
					$(dceshape).attr('fill','url(#pattern-'+id_scope+')');
				}
				tweenString += '.to("'+dceshape+'", '+getCustomData_speed(0)+', {onStart: moveFnStart, onStartParams:[0], onComplete: myFunction1, onCompleteParams:[0], morphSVG:`'+shapes[0].points+'`, ease: '+getCustomData_easing(0)+'.'+getCustomData_morph(0)+', attr:{'+fill_element+'"stroke-width":'+shapes[0].stroke.width+', stroke:"'+shapes[0].stroke.color+'"}}, "+='+getCustomData_duration(0)+'")';
				tweenString += ';';

				tweenSVG += '.to("'+dceshape_svg+'", '+getCustomData_speed(0)+', {rotation:'+shapes[0].svg.rotate+', x:'+shapes[0].svg.x+', y:'+shapes[0].svg.y+', ease: '+getCustomData_easing(0)+'.'+getCustomData_morph(0)+'}, "+='+getCustomData_duration(0)+'")';
				tweenSVG += ';';
			}

			eval(tweenString);
			eval(tweenSVG);

			is_running = true;
			if( run == 'paused' && elementorFrontend.isEditMode() ){
				ferma();
			}

			if( trigger_svg == 'rollover' || trigger_svg == 'scroll' ){
				ferma();
			}

			// alla fine dell'intero ciclo
			tl.eventCallback("onRepeat", myFunction1, ["param1","param2"]);


			if(repeat_morph != 0){
				tl.repeat(repeat_morph);
				tlpos.repeat(repeat_morph);
			}


			if(elementSettings.yoyo){
				if(tl.reversed()) tl.repeatDelay(elementSettings.duration_morph.size);
				if(tlpos.reversed()) tlpos.repeatDelay(elementSettings.duration_morph.size);

				tl.yoyo(true);
				tlpos.yoyo(true);
			}

		};

		var myFunction1 = function(id_step){
			// ad ogni trasformazione
			$('#dce-svg-'+id_scope).attr('data-morphid',id_step);
		};

		var movetoFn = function(id_step){
			if(transitionTl) transitionTl.kill($(dceshape));
			if(transitionTlpos) transitionTl.kill($(dceshape_svg));
		};
		var moveFnStart = function(id_step){
			if(enable_image){
				transitionImgAll = gsap.to(
					'#dce-svg-'+id_scope+' pattern image.dce-shape-image',
					{
						duration:getCustomData_speed( id_step ),
						opacity:0,
						ease: + (
							getCustomData_easing( id_step ) + '.' + getCustomData_morph( id_step )
						)
					}
				);
				transitionImg = gsap.to(
					'#dce-svg-'+id_scope+' pattern image#img-patt-'+id_step,
					{
						duration: getCustomData_speed(id_step),
						opacity: 1,
						ease: + (getCustomData_easing(id_step)+'.'+getCustomData_morph(id_step))
					}
				);
			}
		};

		var interrompi = function(){
			tl.pause(0);
			tlpos.pause(0);
			is_running = false;
		};

		var ferma = function(){
			if(transitionTl)transitionTl.pause();
			if(transitionTlpos)transitionTlpos.pause();
			tl.pause();
			tlpos.pause();
			is_running = false;
		};
		var riproduci = function(){
			tl.play();
			tlpos.play();
			is_running = true;
		};

		var moveToStep = function(step){
			get_data_shape();

			if (typeof shapes[step] !== "undefined") {
				if(transitionTl) transitionTl.kill($(dceshape));
				if(transitionTlpos) transitionTlpos.kill($(dceshape_svg));

				var fill_element = 'fill:"'+shapes[step].fill.color+'", ';
				if(enable_image && (shapes[step].fill.image || pattern_image)){
					fill_element = '';
					$(dceshape).attr('fill','url(#pattern-'+id_scope+')');
				}
				var tweenString = 'transitionTl.to("'+dceshape+'", '+getCustomData_speed(step)+', {onStart: moveFnStart, onStartParams:['+step+'], onComplete: movetoFn, onCompleteParams:['+step+'], morphSVG:`'+shapes[step].points+'`, ease: '+getCustomData_easing(step)+'.'+getCustomData_morph(step)+', attr:{'+fill_element+'"stroke-width":'+shapes[step].stroke.width+', stroke:"'+shapes[step].stroke.color+'"}});';
				var tweenStringPos = 'transitionTlpos.to("'+dceshape_svg+'", '+getCustomData_speed(step)+', {rotation: '+shapes[step].svg.rotate+', x:'+shapes[step].svg.x+', y:'+shapes[step].svg.y+', ease: '+getCustomData_easing(step)+'.'+getCustomData_morph(step)+'});';

				eval(tweenStringPos);
				eval(tweenString);

			}
		};

		var playShapeEl = function() {

			if(transitionTl) transitionTl.kill($(dceshape));
			if(transitionTlpos) transitionTlpos.kill($(dceshape_svg));

			transitionTl = new gsap.timeline();
			transitionTlpos = new gsap.timeline();

			function repeatOften() {

				if(run != $('#dce-svg-'+id_scope).attr('data-run')){
					get_data_anim();
					run = $('#dce-svg-'+id_scope).attr('data-run');
					if( run == 'running'){
						riproduci();
					}else{
						ferma();
					}
				}

				if(!is_running){
					if( step != $('#dce-svg-'+id_scope).attr('data-morphid')){
						step = $('#dce-svg-'+id_scope).attr('data-morphid');

						moveToStep(step);
					}
				}

			  // Do whatever
			  requestAnimationFrame(repeatOften);

			}
			requestAnimationFrame(repeatOften);
		};

		var active_scrollAnalysi = function($el){
			if($el){

				var runAnim = function(dir){

					step = $('#dce-svg-'+id_scope).attr('data-morphid');
					if(dir == 'down'){

						if(one_by_one){
							if(step < numberOfElements-1){
								step ++;
							}else{
								step = 0;
							}
							moveToStep(step);
						}else{
							riproduci();
						}
					}else if(dir == 'up'){
						if(one_by_one){

						}else{
							interrompi();
						}
					}
					$('#dce-svg-'+id_scope).attr('data-morphid',step);
				};
				var waypointOptions = {
					offset: '100%',
					triggerOnce: false
				};
				elementorFrontend.waypoint($($el), runAnim, waypointOptions);
			}
		};

		var mouseenterFn = function(){

			step = 1;
			$('#dce-svg-'+id_scope).attr('data-morphid',step);

			if(!is_running)
			moveToStep(step);
		};
		var mouseleaveFn = function(){

			step = 0;
			$('#dce-svg-'+id_scope).attr('data-morphid',step);

			if(!is_running)
			moveToStep(step);

		};

		// in frontend rendo obbligatorio l'animazione se sono con più di un elemento
		if(!elementorFrontend.isEditMode() && contentElemsTotal > 1 && elementSettings.svg_trigger == 'animation'){
			$('#dce-svg-'+id_scope).attr('data-run','running');
		}

		// pulisco tutto
		if(elementorFrontend.isEditMode()){
			if(transitionTl) transitionTl.kill($(dceshape));
			if(transitionTlpos) transitionTlpos.kill($(dceshape_svg));
			$('.elementor-element[data-id='+id_scope+']').off('mouseenter','svg');
			$('.elementor-element[data-id='+id_scope+']').off('mouseleave','svg');
			$('.elementor-element[data-id='+id_scope+']').off('touchstart','svg');
			$('.elementor-element[data-id='+id_scope+']').off('touchend','svg');
		}

		setTimeout(function(){
			get_data_anim();
			get_data_shape();

			if(elementSettings.svg_trigger == 'animation'){

				createTween();

				// Comincia L'animazione ...........
				if( elementorFrontend.isEditMode() && contentElemsTotal > 1) playShapeEl();

			}else if(elementSettings.svg_trigger == 'rollover'){

				if(transitionTl) transitionTl.kill($(dceshape));
				if(transitionTlpos) transitionTlpos.kill($(dceshape_svg));

				transitionTl = new gsap.timeline();
				transitionTlpos = new gsap.timeline();

				// porto in stop la sequenza...
				// e dat-run in pauded

				$('.elementor-element[data-id='+id_scope+']').on('mouseenter','svg', mouseenterFn);
				$('.elementor-element[data-id='+id_scope+']').on('mouseleave','svg', mouseleaveFn);
				$('.elementor-element[data-id='+id_scope+']').on('touchstart','svg', mouseenterFn);
				$('.elementor-element[data-id='+id_scope+']').on('touchend','svg', mouseleaveFn);

			}else if(elementSettings.svg_trigger == 'scroll'){
				if(one_by_one){
					if(transitionTl) transitionTl.kill($(dceshape));
					if(transitionTlpos) transitionTlpos.kill($(dceshape_svg));

					transitionTl = new gsap.timeline();
					transitionTlpos = new gsap.timeline();
				}else{

					if(playpause_control == 'paused'){
						ferma();

					}else{
						createTween();
					}

					// Comincia L'animazione ...........
					if( elementorFrontend.isEditMode() && contentElemsTotal > 1) {
						playShapeEl();
					}
				}

				active_scrollAnalysi( '#dce-svg-'+id_scope );
			}

		},100);

	};
	// Make sure you run this code under Elementor..
	$( window ).on( 'elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/dyncontel-svgmorphing.default', WidgetElements_SvgMorphHandler );
	} );
} )( jQuery );
