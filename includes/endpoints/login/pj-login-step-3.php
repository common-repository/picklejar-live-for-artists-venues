<?php
/**
 * PickleJar Live for Artists & Venues - Login Step 3.
 *
 * @since 1.0.0
 * @package PickleJar Live for Artists & Venues
 */

$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ?? '' ) );
if ( isset( $nonce ) && wp_verify_nonce( $nonce, 'pj-login-step-3-nonce' ) ) :
	$response['success'] = true;
	$response['content'] = '';

	$inputs           = '';
	$select           = '';
	$entity_type_list = array(
		'Artists' => '<svg viewBox="0 0 8.466666 8.4666661" height="31.999998" width="31.999998"><path fill=#000000; stroke-width=0.0141225" d="M 2.3458778,8.3695775 C 2.1233046,8.2977551 2.0073903,8.1661111 2.0070754,7.9848035 2.0067548,7.8110125 2.1115234,7.6861422 2.3246938,7.6061772 c 0.1305367,-0.048967 0.3555076,-0.045114 0.4951712,0.00848 0.01743,0.00669 0.0203,-0.067207 0.0203,-0.5226504 0,-0.3586696 0.00508,-0.5415835 0.01568,-0.5648528 0.018314,-0.040195 0.067373,-0.065773 0.1261534,-0.065773 0.05481,0 0.4239414,0.1473134 0.5179,0.2066845 C 3.6437403,6.7589595 3.7331596,6.9291935 3.732567,7.111018 3.7320843,7.2585488 3.6723894,7.4882667 3.623384,7.5311636 3.556093,7.5900646 3.4356289,7.5598081 3.4066678,7.4767314 3.3938515,7.4399633 3.3975823,7.405583 3.4276982,7.2829746 3.4695544,7.1125696 3.466111,7.049449 3.4103434,6.9648057 3.3834018,6.923914 3.3493332,6.8988784 3.2732674,6.8640764 3.2175944,6.8386046 3.157745,6.8134116 3.1402685,6.8080915 l -0.031775,-0.00967 v 0.636618 c 0,0.6169263 -9.138e-4,0.6385662 -0.029606,0.6996014 C 3.0277858,8.2433538 2.9160407,8.3260944 2.7563979,8.3734238 2.652449,8.4042423 2.4473377,8.4023217 2.3458778,8.3695792 Z m 0.393969,-0.2833658 c 0.031646,-0.016137 0.068133,-0.045523 0.081082,-0.065285 0.022235,-0.033935 0.022006,-0.037994 -0.00413,-0.072993 -0.050663,-0.067862 -0.1256175,-0.095488 -0.2590848,-0.095488 -0.1334671,0 -0.2084224,0.027626 -0.2590895,0.095488 -0.026102,0.034961 -0.02634,0.039089 -0.0042,0.072881 0.025113,0.038327 0.088569,0.076513 0.1573685,0.0947 0.07038,0.018605 0.2249682,0.00288 0.2880507,-0.029303 z M 0.78861563,7.0154569 0.69378981,6.9198993 1.0395755,6.57433 1.3853611,6.2287605 1.2733294,6.1155795 C 1.1603476,6.0014384 1.1345898,5.9540291 1.1529272,5.8939679 1.1584341,5.8759309 1.2631025,5.7594913 1.3855234,5.6352129 1.5468236,5.4714665 1.6835867,5.3120451 1.8822521,5.0561903 2.0330318,4.862006 2.1616582,4.6945632 2.168088,4.6840955 2.1764499,4.6704831 2.0452545,4.5297321 1.7073569,4.1898078 1.4264673,3.9072338 1.2254978,3.6938821 1.2116578,3.6635679 1.1255393,3.4749368 1.1653634,3.2809569 1.3168226,3.1513131 c 0.080982,-0.069317 0.1762729,-0.098664 0.2930433,-0.090247 0.153866,0.011091 0.1985394,0.043278 0.5510622,0.3970423 l 0.3163991,0.3175143 0.071927,-0.078699 c 0.074144,-0.081126 0.1833389,-0.1443917 0.249649,-0.1446425 0.032324,-1.067e-4 0.038315,-0.00846 0.053372,-0.074281 0.034203,-0.1495158 0.1814451,-0.2942209 0.3206149,-0.3150905 0.048192,-0.00723 0.062705,-0.015447 0.062705,-0.035515 0,-0.014361 0.01314,-0.061231 0.0292,-0.1041555 0.090826,-0.2427537 0.4063491,-0.3402777 0.6180761,-0.191039 l 0.063115,0.044488 0.1946882,-0.1943157 0.1946883,-0.1943157 -0.062657,-0.06969 c -0.054568,-0.060693 -0.062658,-0.077915 -0.062658,-0.1333946 0,-0.063183 0.00175,-0.065478 0.2140362,-0.2805526 0.2210103,-0.2239151 0.2689427,-0.256765 0.3489999,-0.2391818 0.039522,0.00868 0.040616,0.00729 0.049993,-0.063397 0.00527,-0.039749 0.02889,-0.1256165 0.052482,-0.1908163 0.1296752,-0.3583755 0.4418657,-0.62716562 0.822505,-0.70816221 0.1515852,-0.032256 0.4058164,-0.0189171 0.5526853,0.0289972 0.2407632,0.0785466 0.4643096,0.24826481 0.6004806,0.45588981 0.420866,0.6417088 0.068362,1.5159996 -0.6803685,1.6874712 -0.1347513,0.03086 -0.1362204,0.031647 -0.1231479,0.06603 0.025747,0.067721 -0.00897,0.1175988 -0.2422925,0.3480705 l -0.2257159,0.2229607 -0.063513,-0.00716 -0.063513,-0.00716 0.1307892,0.1362635 c 0.1464845,0.1526164 0.1845867,0.2166446 0.3275373,0.5504087 0.050607,0.1181582 0.1121165,0.2447681 0.1366873,0.281356 0.024571,0.036587 0.4043288,0.4272113 0.8439052,0.8680531 l 0.7992303,0.8015291 -0.095346,0.09525 -0.095346,0.095249 L 6.6625486,5.5857857 C 5.7240301,4.6442403 5.8083059,4.7466909 5.6301634,4.3307602 5.508382,4.0464215 5.4448838,3.9572681 5.2258314,3.763064 l -0.099883,-0.088554 -0.09404,0.09404 -0.094041,0.094041 0.098223,0.098982 0.098224,0.098982 -0.098106,0.098864 -0.098106,0.098864 -0.60764,-0.6055103 C 3.9188779,3.2426319 3.7114301,3.0454675 3.6875152,3.0416996 3.5443897,3.0191497 3.4642904,3.1587353 3.5533516,3.2755011 c 0.05016,0.065763 0.047889,0.1391319 -0.00598,0.1929951 -0.054756,0.054756 -0.1152131,0.054345 -0.1973962,-0.00134 -0.070771,-0.047955 -0.08288,-0.049753 -0.1490086,-0.022122 -0.085937,0.035907 -0.1054264,0.1355489 -0.04305,0.2200943 0.047323,0.064144 0.047833,0.1441045 0.00122,0.1907218 -0.048179,0.048179 -0.100776,0.058176 -0.1545235,0.029371 -0.024424,-0.01309 -0.06273,-0.033765 -0.085125,-0.045944 -0.033502,-0.018219 -0.050305,-0.019271 -0.094816,-0.00594 C 2.72406,3.8634813 2.6951188,3.9692379 2.7624913,4.0605558 2.8227773,4.1422698 2.8067033,4.2383344 2.7257353,4.280204 2.6377783,4.325689 2.6269103,4.317298 2.1192386,3.8119351 L 1.6454584,3.340304 h -0.060429 c -0.04796,0 -0.068947,0.00852 -0.1017098,0.041281 -0.045897,0.045897 -0.052534,0.094198 -0.021107,0.1536045 0.011096,0.020975 0.5210944,0.5379572 1.1333307,1.1488498 l 1.1131566,1.1107145 0.148286,0.070581 c 0.081558,0.03882 0.2181926,0.1009134 0.3036337,0.1379865 0.085441,0.037073 0.1903008,0.089868 0.2330217,0.1173219 0.072084,0.046324 0.5366547,0.4889597 0.5366547,0.5113163 0,0.0055 -0.042897,0.051786 -0.095327,0.1028521 L 4.7396429,6.8276596 4.5136831,6.6041989 C 4.3894046,6.4812955 4.2670788,6.3666808 4.2418473,6.3494994 4.2166157,6.3323196 4.081558,6.2686146 3.9417192,6.2079357 3.8018806,6.1472564 3.6515913,6.0761869 3.6077432,6.050003 3.563895,6.0238192 3.4413155,5.918767 3.3353442,5.8165533 L 3.1426699,5.6307111 2.7689887,5.9198948 C 2.4832473,6.1410241 2.3419034,6.2620306 2.1683794,6.4340856 1.9459104,6.654672 1.9402578,6.6590927 1.8806732,6.6590927 c -0.057041,0 -0.068082,-0.00713 -0.1795587,-0.1159389 L 1.5823344,6.4272156 1.2402222,6.7691143 C 1.0520608,6.9571582 0.89480956,7.1110128 0.8907759,7.1110128 c -0.004035,0 -0.0500057,-0.043 -0.1021592,-0.095558 z M 1.9825196,6.224556 2.0773849,6.1289579 1.879848,5.9317983 1.6823105,5.7346393 1.5832594,5.8329314 1.4842083,5.9312235 1.678302,6.1256892 c 0.1067511,0.1069561 0.1975262,0.1944656 0.2017227,0.1944656 0.0042,0 0.050318,-0.04302 0.1024949,-0.095599 z M 2.6120365,5.6904292 C 2.7879951,5.5537996 2.9335653,5.4377736 2.9355252,5.432594 2.9374836,5.4274168 2.8133882,5.2975945 2.6597545,5.1441047 L 2.3804203,4.865032 2.1225478,5.1974886 1.8646752,5.5299451 2.0735007,5.7339177 C 2.188355,5.846102 2.284528,5.9381052 2.2872187,5.9383685 2.2899087,5.9386271 2.4360774,5.8270589 2.6120365,5.6904292 Z M 4.933476,3.4709395 5.1269318,3.2767551 4.8344292,2.9756642 4.5419266,2.6745739 4.3444084,2.8732691 4.1468895,3.0719643 4.3240373,3.2520333 c 0.097431,0.099038 0.2272093,0.232499 0.2883958,0.2965801 0.061186,0.06408 0.1149246,0.1165104 0.1194179,0.1165104 0.00449,0 0.095225,-0.087383 0.201625,-0.1941843 z M 5.6260688,3.1741523 5.7208781,3.0786113 5.2268813,2.5847654 4.7328849,2.0909202 l -0.099162,0.098403 -0.099163,0.098403 0.4908338,0.4909842 c 0.2699591,0.2700412 0.4942162,0.4909843 0.4983497,0.4909843 0.00413,0 0.05018,-0.042994 0.1023254,-0.095542 z M 6.1452776,2.696312 C 6.2216716,2.673646 6.3465012,2.616443 6.3821705,2.587756 6.3965285,2.576209 6.3801105,2.552267 6.3080145,2.479615 l -0.092596,-0.093311 0.098855,-0.098098 0.098855,-0.098097 0.08866,0.087873 c 0.048763,0.04833 0.093176,0.087872 0.098695,0.087872 0.019282,0 0.109727,-0.1983594 0.1303741,-0.2859298 C 6.817969,1.7104596 6.6329008,1.3158724 6.2931173,1.1466063 6.0457076,1.0233565 5.7453816,1.0310501 5.5108442,1.1666454 L 5.4346964,1.2106693 5.8213015,1.5974654 6.2079062,1.9842623 6.1095239,2.0834042 6.0111417,2.182546 5.6253803,1.7975656 C 5.1911618,1.3642257 5.233501,1.3878041 5.162306,1.5396872 5.1043227,1.6633836 5.0819595,1.7796419 5.0882932,1.9244535 l 0.00577,0.1318351 0.3303976,0.3306224 c 0.2587763,0.2589522 0.3397563,0.332008 0.3735685,0.3370142 0.076041,0.011258 0.2675722,-0.00397 0.347252,-0.027613 z M 7.0881815,4.3298589 C 6.8047972,4.2392494 6.6709106,4.0067321 6.7795273,3.7938261 6.8159023,3.7225253 6.9224453,3.627258 7.0113069,3.5865756 c 0.1384657,-0.063392 0.3546838,-0.074494 0.4945903,-0.025395 0.030104,0.010565 0.056751,0.019209 0.059214,0.019209 0.00247,0 0.00643,-0.1816516 0.00882,-0.4036701 l 0.00434,-0.4036709 0.046121,-0.041189 c 0.025367,-0.022653 0.062224,-0.041189 0.081906,-0.041189 0.052955,0 0.1184572,0.038793 0.1338468,0.07927 0.00785,0.020642 0.013458,0.2883372 0.013378,0.6383667 C 7.8533628,4.0100999 7.8533161,4.0112448 7.820433,4.0817375 7.7409529,4.2520442 7.5363645,4.3579147 7.2897239,4.3563667 7.2134341,4.355884 7.1389553,4.3460933 7.0881848,4.3298589 Z M 7.4897594,4.0374167 c 0.1089578,-0.054747 0.1060077,-0.132224 -0.00729,-0.1915359 -0.090782,-0.047524 -0.2745469,-0.045716 -0.3705225,0.00365 -0.086135,0.0443 -0.1111638,0.095298 -0.069412,0.1414336 0.088761,0.09808 0.3006754,0.1200936 0.4472278,0.046457 z M 1.3887975,2.2451005 C 1.1659756,2.1851114 1.0321203,2.0369844 1.0327951,1.85114 1.0331692,1.7416528 1.0659856,1.6698625 1.1514514,1.5913811 1.2603387,1.4913927 1.4191227,1.4333283 1.5816592,1.4340627 c 0.064201,2.586e-4 0.2110972,0.027805 0.2593388,0.048576 0.023293,0.010029 0.024714,-0.020533 0.024714,-0.53144366 0,-0.590471 -7.792e-4,-0.58452695 0.080642,-0.6163819 0.064339,-0.0251718 1.3900025,-0.28509588 1.4539249,-0.28507244 0.048269,1.379e-5 0.065295,0.0077822 0.095327,0.04347308 l 0.036562,0.043451 v 0.74563364 c 0,0.80033148 -5.344e-4,0.80669548 -0.073331,0.90519258 C 3.4100114,1.8535373 3.2779459,1.935224 3.1748938,1.9631189 3.0466341,1.9978371 2.8232043,1.9876999 2.708396,1.9419537 2.6107001,1.9030256 2.4974966,1.8057835 2.455681,1.7248696 2.4138846,1.6439942 2.413376,1.5042206 2.4545949,1.4260722 2.4948696,1.349711 2.5995722,1.2553131 2.6898232,1.2139951 c 0.1384657,-0.063392 0.354684,-0.074494 0.4945903,-0.025395 0.030104,0.010565 0.057114,0.019209 0.060021,0.019209 0.00291,0 0.00528,-0.1929146 0.00528,-0.42869955 V 0.35040991 l -0.483692,0.0967362 C 2.4999906,0.50035172 2.2489631,0.5507262 2.2081846,0.55909025 L 2.1340415,0.57429778 2.1337209,1.2547068 C 2.1333468,2.0227377 2.1366246,1.9978521 2.0220655,2.109027 1.8815174,2.2454239 1.6081609,2.3041604 1.3887888,2.2451 Z M 1.7621333,1.955999 C 1.7944893,1.942083 1.8320013,1.913852 1.8454913,1.893263 1.8690143,1.857363 1.8688573,1.854267 1.8416713,1.817798 1.7864503,1.743712 1.7305897,1.72328 1.583258,1.72328 c -0.115419,0 -0.1423511,0.00469 -0.1927619,0.033577 -0.079419,0.045507 -0.1014103,0.087655 -0.070415,0.1349589 0.063013,0.09617 0.2907775,0.1292408 0.4420478,0.064183 z M 3.1639405,1.6706195 c 0.043168,-0.021436 0.069098,-0.045205 0.078174,-0.071657 0.017656,-0.051463 7.586e-4,-0.077937 -0.078415,-0.1228272 -0.052922,-0.030007 -0.080284,-0.035306 -0.1823086,-0.035306 -0.09786,0 -0.1326451,0.0062 -0.1882484,0.03358 -0.080039,0.039405 -0.1075321,0.084595 -0.081294,0.1336197 0.055335,0.1033937 0.3013337,0.1374511 0.452092,0.062591 z"></path></svg>',
		// 'Venues'  => '<svg viewBox="0 0 8.466666 8.4666661" height="31.999998" width="31.999998"><path fill="#000000" stroke-width="0.0131781" d="M 1.050087,8.3831895 C 0.97579731,8.3566859 0.91384999,8.3101387 0.86771812,8.2461576 0.79604498,8.1467527 0.79025705,8.1058741 0.79014511,7.6983009 L 0.79004056,7.3281142 0.74072424,7.29931 C 0.64505219,7.2434307 0.47640354,7.0852227 0.38983854,6.9701465 0.23801022,6.7683112 0.13896109,6.5439188 0.09799015,6.3089741 0.06866526,6.1408129 0.06971955,4.7417726 0.0992686,4.5963987 0.14395667,4.376634 0.24781104,4.1883623 0.42124418,4.0127061 0.55662151,3.8755938 0.66894683,3.8004246 0.83454199,3.736124 1.0278467,3.6610636 1.0794171,3.6581189 2.2002869,3.6581414 c 0.6620425,1.3e-5 1.0385419,0.00481 1.0703109,0.013631 0.065554,0.018205 0.1273155,0.072252 0.1568376,0.1372447 0.030646,0.067466 0.033338,0.3005759 0.00502,0.4342685 -0.054864,0.2589785 -0.2447085,0.5007441 -0.4859536,0.6188596 -0.1525914,0.07471 -0.2254738,0.08905 -0.4917196,0.096752 l -0.2396885,0.00693 0.07717,0.05801 c 0.1488047,0.1118586 0.4249053,0.4423813 0.4013621,0.4804748 -0.00414,0.00671 -0.030687,0.024001 -0.058985,0.038437 -0.028299,0.014438 -0.071566,0.043909 -0.096148,0.065492 l -0.0447,0.039243 -0.063241,-0.095331 C 2.2998571,5.3551405 2.112624,5.1926182 1.8965309,5.0886142 1.7419443,5.0142122 1.6001568,4.977174 1.4293677,4.9665801 1.303145,4.9587502 1.2957805,4.9566053 1.2547582,4.9157199 c -0.063765,-0.063552 -0.05365,-0.1494587 0.022924,-0.1946917 0.03331,-0.019677 0.1044132,-0.021946 0.6879583,-0.021946 0.7056781,0 0.7286778,-0.00196 0.8788279,-0.074706 C 2.883482,4.6054722 2.953084,4.5530592 2.9991398,4.507902 3.1331634,4.3764908 3.2006529,4.201873 3.2015606,3.9841703 l 2.642e-4,-0.062596 H 2.164892 c -1.1571348,0 -1.1161008,-0.00289 -1.31535701,0.092506 -0.17895118,0.085672 -0.32661264,0.2329821 -0.41583823,0.4148501 -0.0971723,0.1980665 -0.0957479,0.1848428 -0.10150827,0.9422338 -0.003443,0.4527216 -1.8921e-4,0.7225458 0.009773,0.8104529 0.0188755,0.1665694 0.0819155,0.3548671 0.16652926,0.4974233 0.0752326,0.1267503 0.25963648,0.3203905 0.38556215,0.4048737 0.1812095,0.121573 0.4088494,0.1991778 0.6402556,0.2182698 0.1050007,0.00866 0.1221921,0.013822 0.1548427,0.046473 0.04735,0.04735 0.04969,0.1144634 0.00577,0.1655224 -0.025953,0.030173 -0.044007,0.036934 -0.1117031,0.041831 -0.1026267,0.00742 -0.2684824,-0.015977 -0.4061354,-0.057305 -0.059031,-0.017722 -0.1109204,-0.032223 -0.1153083,-0.032223 -0.00438,0 -0.00798,0.1336369 -0.00798,0.2969706 0,0.3281155 0.00414,0.3470166 0.081905,0.3741264 0.02778,0.00969 0.4066249,0.014165 1.1977557,0.014165 H 3.490579 l 0.055484,0.095541 C 3.576579,8.2998331 3.610139,8.3561692 3.620639,8.372477 L 3.639732,8.402127 2.3698249,8.4015588 C 1.3385466,8.4011047 1.090552,8.3976387 1.0500734,8.383198 Z m 2.9488189,0.00568 C 3.9770476,8.3821349 3.9377359,8.3541883 3.911544,8.3267758 3.8566006,8.2692717 3.7176737,8.0327086 3.7067396,7.9780381 c -0.011418,-0.057097 0.01652,-0.1055884 0.089554,-0.155426 0.153473,-0.1047294 0.2882341,-0.2946789 0.333477,-0.470045 0.02691,-0.1043036 0.02691,-0.3004132 0,-0.4047167 C 4.0952704,6.8141228 4.0234762,6.6930192 3.913724,6.5834195 3.7995102,6.4693649 3.6973312,6.4101116 3.5497922,6.3723777 3.3712864,6.3267234 3.1687434,6.3486044 3.0058682,6.4311402 2.9218031,6.4737394 2.8550057,6.4751971 2.8153981,6.4352966 2.7992102,6.4189894 2.7481348,6.3382266 2.7018984,6.255825 2.6398324,6.1452131 2.6162084,6.0891085 2.6116282,6.0414434 2.6041052,5.963161 2.6335634,5.8777139 2.6848318,5.8290899 2.7046334,5.8103082 3.6400095,5.2627274 4.763442,4.6122428 6.7847089,3.4418988 6.8069004,3.4294826 6.8877533,3.4236822 c 0.065578,-0.00471 0.093594,-1.045e-4 0.141928,0.023291 0.06583,0.031868 0.094328,0.068303 0.2023667,0.2587267 0.092893,0.1637294 0.089645,0.2184299 -0.016975,0.2858822 -0.054917,0.034743 -0.1615724,0.1371262 -0.2159624,0.207311 -0.044738,0.057731 -0.1044226,0.1869892 -0.1275154,0.2761613 -0.030743,0.1187107 -0.020839,0.3425182 0.020327,0.4593627 0.038432,0.1090849 0.1414726,0.2620595 0.227754,0.3381237 0.1300747,0.1146726 0.3583709,0.2032001 0.5256118,0.20382 0.092307,3.409e-4 0.2438759,-0.035158 0.3383826,-0.079253 0.092764,-0.043283 0.1528155,-0.049241 0.1954317,-0.019393 0.015492,0.01085 0.068324,0.089424 0.117405,0.1746096 0.075227,0.1305636 0.090128,0.1666639 0.0949,0.2299218 0.00682,0.090394 -0.029714,0.1783958 -0.094291,0.2271322 C 8.2654663,6.0332654 6.0434702,7.3229959 4.3373205,8.307795 4.2078856,8.382506 4.1779724,8.394494 4.1132929,8.397574 4.0722369,8.399534 4.0207629,8.395614 3.9989054,8.388874 Z M 5.0581354,7.5939037 C 5.5753663,7.2947059 5.998464,7.045458 5.9983521,7.0400201 5.998181,7.0316534 4.7883813,4.9257065 4.7756687,4.9116454 c -0.00803,-0.00889 -1.9032831,1.0942607 -1.9032831,1.1078258 0,0.020205 0.071023,0.1423517 0.082778,0.142367 0.00566,7.4e-6 0.045152,-0.011727 0.087751,-0.026078 0.1179209,-0.039725 0.2978372,-0.056082 0.433387,-0.039401 0.3621993,0.044572 0.6628638,0.2581046 0.825156,0.5860273 0.085752,0.1732684 0.1046436,0.2571266 0.1053919,0.4678229 7.104e-4,0.2038182 -0.023665,0.3134122 -0.1064347,0.4782527 -0.056915,0.1133507 -0.1000392,0.17184 -0.2109432,0.2861029 l -0.083165,0.085683 0.039498,0.069159 c 0.021725,0.038037 0.046792,0.069009 0.055705,0.068826 0.00892,-1.833e-4 0.439395,-0.2451313 0.9566258,-0.5443293 z M 4.9057527,7.0645102 c -0.044005,-0.030822 -0.061885,-0.1003227 -0.039534,-0.1536716 0.014853,-0.035453 0.046292,-0.059895 0.1624828,-0.1263219 0.079237,-0.0453 0.1575228,-0.082363 0.1739695,-0.082363 0.071959,0 0.1340185,0.063539 0.1340185,0.1372129 0,0.050765 -0.047611,0.095614 -0.1827056,0.1721052 -0.1365293,0.077303 -0.1955757,0.08992 -0.2482312,0.053038 z M 4.6030482,6.5119267 c -0.049707,-0.021977 -0.070223,-0.055767 -0.070223,-0.1156598 0,-0.064216 0.024873,-0.090896 0.1544438,-0.1656764 0.157285,-0.090775 0.1818484,-0.099231 0.2361673,-0.081304 0.05661,0.018683 0.083801,0.058114 0.083801,0.1215214 0,0.064551 -0.026032,0.090475 -0.1779043,0.1771616 -0.1400303,0.079928 -0.1706134,0.088573 -0.2262848,0.063957 z M 4.2748011,5.9490972 c -0.03497,-0.016969 -0.071428,-0.078344 -0.071428,-0.1202485 0,-0.052687 0.046235,-0.096716 0.1827056,-0.1739862 0.1487407,-0.084218 0.2064867,-0.093373 0.2588236,-0.041036 0.043976,0.043976 0.052537,0.088928 0.027131,0.1424647 -0.016688,0.035169 -0.051796,0.062163 -0.1642701,0.1263089 -0.1421181,0.081052 -0.1804311,0.091989 -0.2329621,0.066497 z M 7.1882115,6.3602089 C 7.6810722,6.0755625 8.0974624,5.8344247 8.1135227,5.8243475 l 0.029201,-0.018321 -0.02789,-0.049409 c -0.015338,-0.027174 -0.035442,-0.061857 -0.044674,-0.077071 l -0.016787,-0.027662 -0.09654,0.033943 c -0.088228,0.03102 -0.1146955,0.033949 -0.30739,0.034006 C 7.4465303,5.7198983 7.4340552,5.7183301 7.318041,5.6780155 7.1565684,5.6219164 7.0264287,5.5421954 6.9118473,5.429189 6.6081456,5.1296621 6.5093613,4.6817187 6.6596744,4.2856955 6.7112224,4.1498844 6.7796272,4.0454394 6.895045,3.9263155 L 6.9989826,3.8190406 6.9569396,3.7445456 c -0.023123,-0.040972 -0.046674,-0.072755 -0.052336,-0.070629 -0.00566,0.00213 -0.4350713,0.2499666 -0.9542452,0.5507567 -0.6744255,0.3907371 -0.9425349,0.5525352 -0.9389883,0.566658 0.00422,0.016796 1.1478238,2.0051466 1.2037624,2.0929437 0.017657,0.027714 0.022165,0.028838 0.048222,0.012023 C 6.2791625,6.886096 6.6953483,6.6448567 7.1882092,6.3602102 Z M 4.7537668,2.7381224 C 4.7502724,1.1642464 4.7502014,1.160635 4.7224452,1.1014645 4.6599707,0.96828777 4.5193654,0.87637604 4.3770662,0.87569535 4.2154547,0.87492828 4.0947868,0.94665483 4.0223676,1.0865502 l -0.043022,0.083108 -2.292e-4,1.2935965 c -2.515e-4,1.4208048 8.522e-4,1.3991836 -0.079727,1.5585826 -0.022381,0.044271 -0.07433,0.1137824 -0.1154436,0.1544701 L 3.7091938,4.250284 V 3.4171423 c 0,-0.8202304 -4.262e-4,-0.8340611 -0.027819,-0.8924436 C 3.5918495,2.3338569 3.3583719,2.2449509 3.1718757,2.3306864 3.078165,2.373767 3.0298484,2.417402 2.9837919,2.5005469 l -0.038927,0.070275 -0.00398,0.4118153 -0.00398,0.4118157 H 2.8058004 2.6747134 V 2.8828991 c 0,-0.6080716 -5.114e-4,-0.6111933 -0.1166101,-0.7273028 C 2.4642749,2.0617683 2.3830407,2.030855 2.254546,2.0400792 2.1502985,2.0475624 2.0825814,2.079404 2.0058829,2.1570035 1.8996051,2.2645296 1.8972056,2.2803688 1.8972056,2.8744126 V 3.3944531 H 1.765425 1.6336438 V 2.0310145 c 0,-1.34084778 -4.546e-4,-1.36463106 -0.02693,-1.43541412 C 1.5274554,0.3837657 1.2787686,0.28784733 1.0723156,0.38948374 c -0.0828919,0.0408076 -0.13537792,0.0933314 -0.18472378,0.184857 -0.0238671,0.0442684 -0.0250092,0.10323106 -0.0283927,1.46606306 -0.003318,1.3366474 -0.004864,1.4199493 -0.0263557,1.4201007 -0.0125571,9.45e-5 -0.0702112,0.020844 -0.12812033,0.046124 -0.0579091,0.025279 -0.10683285,0.045962 -0.10871927,0.045962 -0.011256,0 -0.001506,-2.87939645 0.009949,-2.94021079 0.0564312,-0.2994162 0.33140736,-0.52562796 0.63893738,-0.52562796 0.3138382,0 0.591074,0.23450317 0.6387593,0.54030191 0.00756,0.048439 0.013401,0.34483668 0.013458,0.68215324 l 1.046e-4,0.596496 0.050283,-0.033276 c 0.1186539,-0.078521 0.3136492,-0.1152058 0.4571804,-0.08601 0.1853801,0.037709 0.3688541,0.1729628 0.4478301,0.3301313 0.01993,0.039659 0.037578,0.073673 0.039223,0.075585 0.00165,0.0019 0.041464,-0.022324 0.088491,-0.053859 0.1837422,-0.1232128 0.4350446,-0.1352202 0.6367347,-0.030424 0.043488,0.022598 0.083516,0.043103 0.088952,0.045571 0.00544,0.00247 0.00988,-0.2121648 0.00988,-0.4769625 0,-0.3064906 0.00539,-0.5068275 0.014827,-0.5512838 0.024481,-0.1152946 0.089668,-0.23348682 0.1772739,-0.32142118 0.251244,-0.25218373 0.6657037,-0.25172577 0.9158647,0.001022 0.084698,0.0855701 0.1223897,0.14592391 0.1606036,0.25716458 l 0.029425,0.085657 0.00374,1.5036324 c 0.00302,1.2105523 4.546e-4,1.5063457 -0.013154,1.517553 -0.0093,0.00766 -0.068691,0.043798 -0.131996,0.080313 L 4.757277,4.3154895 4.753777,2.7381332 Z M 5.7708457,3.2442958 C 5.6766839,3.2081216 5.6186804,3.1711749 5.5582749,3.1088937 5.3558723,2.9002075 5.3599076,2.5770537 5.5673661,2.3808482 5.6712781,2.2825723 5.7639768,2.2429276 5.9035119,2.2370873 c 0.088074,-0.00368 0.1297522,0.00122 0.1939612,0.022855 0.04498,0.015154 0.084935,0.027552 0.088788,0.027552 0.00386,0 0.007,-0.316463 0.007,-0.703251 0,-0.68826763 5.681e-4,-0.70398026 0.026927,-0.73748338 0.033074,-0.0420471 0.1023924,-0.057518 0.1483472,-0.0331088 0.01829,0.009716 0.1553009,0.12418358 0.304468,0.25437468 C 6.95677,1.3156931 6.985448,1.3510607 6.958971,1.4207006 6.9419931,1.4653546 6.8827281,1.5099979 6.840506,1.5099373 6.7933815,1.5098726 6.7497606,1.480072 6.5886046,1.3378605 L 6.4502384,1.2157568 6.4436501,2.0448384 c -0.0062,0.7800594 -0.00809,0.8325881 -0.031908,0.888383 C 6.3540081,3.0684416 6.2578003,3.169272 6.1329153,3.2254442 6.0520253,3.2618272 5.844594,3.2726292 5.7708469,3.2442972 Z M 6.0285392,2.9873045 C 6.228725,2.8946213 6.2364715,2.6168709 6.0417186,2.514805 6.0194566,2.5031371 5.9697801,2.4940573 5.928021,2.4940176 5.8228774,2.4939229 5.7461749,2.5405849 5.7003618,2.6325197 c -0.044013,0.088322 -0.043384,0.1483663 0.00247,0.2361463 0.029703,0.056859 0.050071,0.077226 0.1069295,0.1069295 0.081886,0.042777 0.1444389,0.046126 0.2187754,0.011708 z M 7.234335,2.8634216 C 7.1298339,2.8334981 7.0488731,2.784148 6.9770971,2.7066216 6.8276582,2.5452098 6.7978877,2.3076715 6.9025531,2.1118361 6.9502001,2.0226853 7.0717498,1.9141143 7.1646942,1.8776853 c 0.1079329,-0.042304 0.2866471,-0.043067 0.3777066,-0.00162 0.035349,0.016091 0.066482,0.029258 0.069185,0.029258 0.0027,0 0.00491,-0.3168078 0.00491,-0.7040168 V 0.49728961 l 0.033301,-0.0395766 c 0.041414,-0.0492181 0.09694,-0.0631073 0.1469354,-0.0367553 0.020461,0.0107843 0.1632173,0.14638699 0.3172363,0.30133812 0.2723612,0.27400931 0.2800344,0.28319537 0.2800344,0.33523847 0,0.076909 -0.048376,0.122994 -0.1291084,0.122994 -0.058558,0 -0.06252,-0.00286 -0.2217432,-0.1602748 L 7.881037,0.85997847 7.8772557,1.6626857 c -0.00374,0.7928519 -0.00414,0.8035774 -0.03282,0.8736134 -0.042036,0.1026435 -0.087222,0.1652155 -0.1619831,0.224311 -0.1221307,0.096539 -0.3152664,0.140848 -0.4481158,0.102807 z M 7.4553114,2.5965494 C 7.5914037,2.5396863 7.6521783,2.387081 7.5917293,2.2540025 7.487501,2.024545 7.1337889,2.0722149 7.1059188,2.3194755 c -0.00982,0.087089 0.00614,0.1474172 0.053179,0.2009847 0.077653,0.088443 0.1946936,0.1185068 0.2962136,0.076089 z"></path></svg>'
	);
	foreach ( $entity_type_list as $entity_type => $icon ) {
		$inputs .= '
    <div class="picklejar-form-group">
        <input type="radio" id="picklejar-' . $entity_type . '" class="picklejar-entity-type" name="entity_type" value="' . $entity_type . '">
        <label class="picklejar-card" for="picklejar-' . $entity_type . '">
            <span class="material-symbols-outlined">check_circle</span>
            ' . $icon . '
            <span class="w-100 picklejar-text-center">
            <strong class="picklejar-entity-type-counter picklejar-entity-type-' . $entity_type . '-counter">
            <div class="picklejar-loader">
                <div class="picklejar-loading"></div>
            </div>
            </strong> ' . ucfirst( $entity_type ) . '</span>
        </label>
    </div>';

		$select .= '<div class="form-group hidden pj-select-entity pj-select-entity-' . $entity_type . '"><select name="entity_id" class="pj-select-' . $entity_type . ' form-control widefat"></select></div>';
	}

	$response['content'] .= ' 
<div class="picklejar-steps step-3">
    <span class="picklejar-step material-symbols-outlined success">check_circle</span>
    <span class="picklejar-step material-symbols-outlined success">check_circle</span>
    <span class="picklejar-step material-symbols-outlined active">radio_button_checked</span>
    <span class="picklejar-step material-symbols-outlined disabled">radio_button_unchecked</span>
</div>';

	if ( isset( $_GET['continue'] ) && 'true' === $_GET['continue'] ) :
		$response['content'] .= '<h1 class="text-primary">You have logged in as a House Account</h1>
    <p>Please wait while we process your data</p>
    <div class="picklejar-loader">
        <div class="picklejar-loading"></div>
    </div>
    <div class="picklejar-hide-validate-input">
        <input type="text" name="name" value="' . get_bloginfo( 'name' ) . '">
    </div>';
	else :
		$response['content'] .= '<h1 class="text-primary">Select your user profile</h1>
    <p>Are you an artist or venue?</p>
    <form
    id="entity-type"
    autoComplete="off"
    >
        <div class="entity-type-input-list picklejar-d-flex">' . $inputs . '</div>
        ' . $select . '
        <div class="picklejar-hide-validate-input" ><input type="text" name="owner_id"></div>
        <div class="picklejar-hide-validate-input">
            <input  type="text" name="name" value="' . get_bloginfo( 'name' ) . '">
        </div>
        <div class="d-block picklejar-text-center">
            <div id="errorMessageBox"><span class="picklejar-invalid"></span></div>
            <button
            type="submit"
            id="entity-type-submit"
            class="picklejar-btn picklejar-btn-primary"
            >
                Next
            </button>
        </div>
    </form>';
	endif;
else :
	$response['success'] = false;
	$response['error']   = 'Invalid nonce';
endif;

return wp_send_json( $response );
