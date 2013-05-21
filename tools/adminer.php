<?php
/** Adminer - Compact database management
* @link http://www.adminer.org/
* @author Jakub Vrana, http://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 3.7.0
*/error_reporting(6135);$mc=!ereg('^(unsafe_raw)?$',ini_get("filter.default"));if($mc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$Ag=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($Ag)$$W=$Ag;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");if($_GET["file"]=="favicon.ico"){header("Content-Type: image/x-icon");echo
lzw_decompress("\0\0\0` \0Ñ\0\n @\0¥CÑË\"\0`E„Q∏‡ˇá?¿tvM'îJd¡d\\åb0\0ƒ\"ô¿f”à§Ós5õœÁ—AùXPaJì0Ñ•ë8Ñ#RäT©ëz`à#.©«cÌX√˛»Ä?¿-\0°Im?†.´M∂Ä\0»Ø(Ãâ˝¿/(%å\0");}elseif($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
lzw_decompress("\n1ÃáìŸåﬁl7úáB1Ñ4vb0òÕfsëºÍn2BÃ—±Ÿòﬁn:á#(ºb.\rDc)»»a7EÑë§¬l¶√±îËi1Ãésò¥Á-4ôáf”	»Œi7Ü≥ÈÜÑéåF√©ñ®aç'3I– d´¬!S±Êæ:4Áß+MdÂgØã¨«É°Óˆtô∞cëÜ£ı„È†b{èH(∆ì—ît1…)t⁄}F¶p0ôï8Ë\\82õDL>Ç9`'C°º€ó889§» éxQÿ˛\0Óe4ôÕQ òl¡≠P±øVâ≈bÒëóΩT4≤\\ûW/ôÊÈ’\nêÄ` 7\"hƒqπË4ZM6£T÷\r≠r\\ñ∂C{h€7\r”x67Œ©∫J á2.3êÂ9àKûÎ¢H¢,å!mî∆Üo\$„π.[\r&Ó#\$≤<¡àfÕ)èZ£\0=œr®è9√‹jŒ™J†Ë0´c,|Œ=ë√‚˘ΩÍö°Rs_6£Ñ›∑≠˚Ç·…ÌÄZ6£2Bæp\\-á1s2…“>éÉ X:\r‹∫ñ»3ªbö√ºÕ-8SLı¿Ìº…K.¸¥-‹“•\rH@ml·:¢Îµ;Æ˙˛¶ÓJ£0LR–2¥!Ëø´ÂAÍà∆2§	m˝—Ì0eI¡≠-:U\r¸„9‘ıMWLª0˚πGcJv2(ÎÎF9é`¬<áJÑ7+Àö~†çï}DJµΩHWÕSN÷«Ôe◊u]1Ã•(O‘L–™<l˛“R[u&™ÉH⁄3èvÚÄõ‹Uàt6∑√\$¡6‡ﬂ‡X\"ò<£ª}:Oã‰<3x≈O§8Û>†ÃÏÏCŒ⁄Ô1É¢Å’HR‚π’SñdÅ9™‡π%µU1ñSnÊa|.˜‘Å`Í†8£†∂:#Ä ‡CŒ2ã∏*[o·Ü4X~ú7j†\\¡√Í6/∂F[NYÉË\\π¨à˙ÍÖn®o5<¨∞lÕ·p‘9“cFZÅs√“|:>6 ñù´k≈v‚©√qs§:ç£pˆ8\rÎ#®»ê^¢ØnZ,B2)O’Œ”RØ˝[Iˆ±’⁄7≤®t“î∑7éÏ(·úÒ¨Wä0¯Û§Ê2x~]Ú;ÒK2å–Va–‡ªÛ~ñr=˙ã(ÀÎ¢,≥õ\rÓ…j*∫B(RÓ2CñN\\åŒˇ“9}a\0≈ï”VR4G´BË©Û¢÷ÏC(s(mmΩÉ¢(wÉsÒnm˙π∑‘B\\lÅMi#;#¯ØU·˛=M-~±ı‰Ê√h)∞5	ÉpåCõ±/,–≤ÿ]És†Öêÿ#CvÏäÅM è¥>˜6@ñhuØ¯Ñ`k¢sõrySë\"ŒÚ‚ìÖ&5≈ué—\"cu/L	#DB»O¥MÄËô¶Ïà∏ctÍ±W6\"¢ü[õá!¥1ÿË`#¥EdénË…ÿLwêmŸ™5g¿∆√AÁU†ÌçF8€ç®\\M-ÿ7áN‚\n:`éR>KI„\$‰¨ój!æM9≤\"O…Í&BÃê≠w‡›ÉòqZ≈‹´Jô\"A√Ö(\rÍ_sÓ»}>Ãú&…H«¥ìn ú6/ê∆CëºfƒÏ89≥@÷√õ£t•î4òÄÃC¡x6f‘€ï5H…PÊ?Ä‘¸D∑Ê≥“ıAä\0·á–G!Ã‚t”ï‘'TY°IÑÃ+îööLn˘]†ﬁêf(1*Z´@ÍÕ\rR¯’ÆƒêgòÇùh!C•âˆp1*Äå2P`ˆ–êf!‹∏8∏#?àAù`∞§4ã8_÷ZèïΩ wN)”ƒR`7—5Z`*Ö;DÌ»ÈùA* ôTTàò+#¸~Jpç¢0GPH≥qå∏≈9©Û]JÙD+eu5-ê‹©…®tûT:´*„a5Glñ≠∫Û6&”⁄\rÂ@≥*¬√=qgÂŒƒi¸Aï© iG≤äØ#·<ióÑÀ¯¯P∫¬L#§†f!‘êwWD‰ÍA¢Û∏®>9íäâ<\"µ/€ ¸•‡da…–?∫í¯Í[ŸyO¸Œ7OTË5∂°˙w&‡kg≤úhˆ˝íö€o!ó]ˆ“û˘‚Óœm>≥ôòËhuî˝á!6\$ƒWá\0⁄¢`[)\r:fﬁ(\$≤p È¡ûòâgÅsãe2—ª .\rò˝’∂q‹ùÿíz¡hm\r·ËáRéle	ÉC.j ÷Yq.'(ƒÔ9ìÑpòaRu0'd¶B©j7a¶4YkR{¡ë¯öÛd¨‚,?lß%¥ª%rÑOÏ\$© bì.Ô‘ªÜÂg8rÖ◊∂πP∆‡‰` <8Tˆﬂ3Ák\nõvÃ˘§88‹Ê™´8eŒŸ°I¡pÂûÛH.ÉP-#_òéWÛõŒ‡é–kE|-(2–Ê}Fü¿cBÌno√9À>;ê∞n]Õt^™∏Òw–\r=˘π£≠9ök}∞–Ä]Gw™ú´‘“”8ÑhÒ—7Õ\$¿ﬂÇÜ.`C)bVlfH†p¯)`∫o¿ùnòµ…µ¶z÷Åe£Ä,Nò∫ñœúUh_]⁄_◊]ìÎ∑næ‡[Ù§ÿªÏÇΩî]«π:Ü>mrCπ36ù—ªü\nÇ›Oj¶fã”Êá~?æπÓæ,‰⁄ê[EªÉ=D€éYk-ã˝πmË§˚´lOı®.f˝¢¸ôËºh±tﬁcBy¸µÊ>´t√ÊEá@>á–");}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
lzw_decompress("f:õågCIº‹\n:ÃÊsaîPi2\nOgc	»e6LÜÛ‘⁄e7∆s)–ã\rè»HGíIí∆∆3aÑÊs'c„—D i6úNå£—Ëú—2H„Ò8úuF§RÖ#≥îîÍr7á#©îv}Ä@†ê`Qåﬁo5öa‘Iú‹,2O'8îR-q:PÕ∆S∏(àaºä*wÉ(∏Á%ø‡pí<F)‹nx8‰zA\"≥Z-C€e∏V'àßÉÅ¶™s¢‰q’˚;NFì1‰≠≤9ÎGºÕ¶'0ô\r¶õŸ»ø±9n`√—ÄúX1©›ÅG3Ã‡tçee9äÆ:NeÌä˝N±–OSÚz¯cëåzlé`5‚„Å»ﬂ	≥3‚Òyﬂ¸8.ä\r„êŒπP‹˙\rÉ@ç£Æÿ\\1\r„ Û\0Ç@2j8ÿó=.∫¶∞ -r»√°®¨0çäËQ®Íä∫hƒbºåÅÏ`Å¿éª^9ãq⁄E!£ í7)#¿∫™* ¿Q∆»ã\0äÿ“1´»Ê\"ëh >Éÿ˙∞––∆⁄-C \"í‰XÆáS`\\ºè§F÷¨h8‡ä≤‚†¬3ß£`X:OÒö,™á´⁄˙)£8ä“<BN–É;>9¡8“Ûácº<á#0L™≥ò 9éîÁ?ß(¯Râ#Íe=™©ù\n´√Í™:*Í 0÷D≥ 9C±à◊@–÷{ZO≥˝Í›8≠¶i™oV®v¢k®Ar™8&£Ö¯..É—cH°E–>ÅH_hÅìŒ’WUŸ5·Ù1çr*ú¶Œˆ^–(€b‡x‹°Y1∞⁄‘&XH‰6Öÿì.9áx∞PÈ\r.`v4áòî∂Üç√8Ë4daXVâ6ÅFè‘’EHH∫fc-^=‰¬ﬁtô›xãY\rö%ˆ´xeè†ÁQ˚,X=1!∫svÈjËkQ2…ì%⁄W?ˆ√≈Æå¥Ê=êdY&Ÿì§VX4ÂŸÄÃ\\ó5–ﬂ„X√¨!◊}‚ÊµNÁ°gv⁄ÉWY*€Q≈Ëi&»l√Œ—µZ#ñ›„Ò†’ë\rAÁ\$e∞v5o#ﬁõ¢ÿ¸ê∂5gc3MTC£L>vŒHÈ‹√˙ñß<`¯∞⁄*†]Ç_à£;%À;Ó⁄Vñ˘iéì¿Ë„4X√Èñ'îå`∫™…„i◊j0g∂O±Ü€•ìiÊåÏ©9∑∆ô€íd›FÍ Ÿk/l≈û∏ñnƒ‹c<b\nâ®8◊`ëHìÎe≈}]\$“≤˙÷‚ Ì∞!Ü¿“√C)±\$ ∞öA◊`Û\0'êïÄ&\0BŒ!Ìå)•Úò¥5E)‰¡‡“¬o\rÑ‘8r`˚»Ã!2Í≠T¡õs=ØDÀ©’>\n/≈l”âíï[˝ò≈†P€‡aá8%ê¯ù!›1v/••SUcoJ®:î4J+ÅBÅ‡ÛáµvØJ¸Ç\r·‚¬b{É†,|\0Ó∞zˆÉc‹™≈Yß≈lÆ\n¸úi.ı‹!‰€)¸¶dmÓJ´Ø—»!'“¡Î B\nC\\i\$Jò\"æÎ÷2»+ÁIkJññÒ\$äëí‚Gôy\$#‹≤i/¶CAbæÃbÇC(·ò:∞ UXòØî2&	«, Q;~/•ıKy9◊ÿ?û\r6æ∞tV È—!∫6áCP≥	hYÎE¡ê”Œÿ‚£ˆlÒ‰èû(ÿñT·“p'3É–C<ÿdcÆÅ∏?∞yCÁÛ˛e0º@&A?»=§Â%≥A:JD&SQò—6RÃ)Aòù–b`0⁄@àÈu9(ç!0R\náF Ñïé¬ä ƒwC\\â©å§œÖr‘‰‹ô°Ó§#ï~ÿ2'\$° :–ÿK¡`h¨≥@â£EbÛ¢[–~°—Èí‚ TÊ≈lf5™≥BR]±{\"-§–\0Ë≠ L>\r«\$@ö\n(&\r¡à9á\0vh*…á∞ñ*∆XÎ!_djàòÉÜÂpyπáèÇ∂ë`·jYûwJÇ\$ÿR™à(uaM+¡ÍnÌxsÇpU^ÄAp`Õ§IÏíH÷\n®fó02…)!4a˘9	¿¢ÍïEwC›–°òìÀ© âL◊P‘›ƒ˛Ai–)Íp¯3‰AÅu‚¿ˆ˝AIêA…Hu	Á!gÕïíUîêâéZU∑¿ºc§*≠¥¿∞MÑ√xf∆:À∆^√Xp+ëV∞ùÜ±·≤KâC#+æ ÷Wh˙CP!»«¿;î[pn\\%ò¥k\0ÑÙ≤≤,⁄®8‡7„¨xQC\nY\rˆbˇ£XvC d\nAº;ÇálF,_wr4RPÔ˘ªHAµ!Ù;ôâ&^Õ≤ùÖ\"6;úÂ≤ÍŒ=˜#CÌèI°∏Ø9fÈ'¨:∏üDY!åˇB+òs°xVÜû8lÁ√ì°\"œÈëÉÕHùU%\"Z6≥‘u\r©e0[¡ïpƒﬂÿa°Ë.Ö¿∂ +^`ú`bß5#CMâ\$≤ ˚IÁÓéÀöAÃPß5C\r˝ SïdÍWN6H[ÔSRΩµ∑Íﬂ\\+èXÎ=k≠ıŒª◊∫˛ºSîÅ∂”r^(¶Éoo∂7ô¨œ©\\huk¢lHaC(m‡Ï˛¯nRBÜ§Uup≥⁄2C1ö[∆|ŸΩ˘beG0–Ÿ\"ÏCG±≤?\$x7–ﬂn≠§\$ZŒ=üZ”¶û√si5ÀfœÌ&Á,Æf”hi∆IŒyé÷nÓ∂2Ú0⁄úDvE¸√TÔxÙ˙MÂ{‡Ù`‹§¡GN#ÈÅÇZ,´¬É/‚R\$î#\\I-	ÆÑ∞ó|ƒ0‡-0˝âNÓ¶P∑…“Å§;s-òvÙñœ“ÜˇΩánwGtÔÖnîè°“di·H◊|•ò4§(Ωº+ºvÚ•›&ÿ≈Öí+K¿£ÃÒôL\nJ\$‘©˝Ü®µ:\\Q<WB\"^óÕÒ§∫WTIB~—ﬂq¨…ûÂ}Û3üŒø\":˛Uá·≠÷|\r5n(nô≠àÅá Ÿ7ÉÃO¡D}B}ãº®Ê \0\rìvo‹ïÑÖ∑ÿÜ_JlÇƒ∞ïH3ë\"Æ[ƒ∏‚Âæ‘KäAèµ`ﬂñ˘Ø¶N…¬¸&(Ç)\"à fˇ&≈\0∞¶ bæÚ®lç„Fé.¬çjrÚÓî˛‚J¬à∆\"P<\$F∞*È|f/ﬁ! ›OÁÁåpR «ôÑF#5g‰b„†ƒ8eRDi∏…0ìPÇ+*¨¸∆˝ôèûkêZ;√pHh¶Æl!ËÅ\0\r\ncõo»/ø˙CBà<py¿NTHΩhÍTÁ	@ÈpxÃ\$¢äÊ∞Ã¿÷48\nÄ“#ÓNU,”àö\$PÈmÚ†YK¸¨\"H†“ ÜR˝L∏˝ãÆ©Dü\0âø‚àÄaêWà`p˚Ô˛˙–gùØÍlP§è¬ˇo˙:LÄ∑ +\0 ]0±<)ÇˆN´xk\n(`cÍÑ+r∑k{m\"‚3.0±ûH1íe*ZoeBÃãê9\r»¯⁄\0RLi•Q®U‘ã`‰¬.îè˚Ò¬ño:≈dÄ¥¬íµT7Qú—V ª…Dhë‚WÊ¥ÎS1Ò	Ò¯gÊ*2Øë,ÜW)∞¡@Áœ∞T@C	Q(Ò,ô≈4Ê#d<“í\0¶! ·\$ò˙2 {es¢¥+Ör ´˛ÕÏŒJvY*åHPr\r§ÇÜÕT‹M\\\\`ºøÌvÌ‡Ê<Ò´&ƒnÙD\\HH»oj^@¢⁄	†¬<ÒäÜØÎ∆8äì*#fÚ©*«˛\r\nTß \\\r≤´*ÁT™^*†⁄…† \$™6oﬁ7Ú–Ree8≥  Á≤°,“•,”,`|9ç∞K2œ0r±+“ß1R÷‰\"» ’*†P*Âæ»ÜM\\\rb‡0\0¬Y\"™\"∫UxÜêŸ`∞±Í»Ä‡QìE\r¿~Q@5 ô5sZ≥^f¿R@Q4»d¿Ç5√b\0û@‘FÛb/Ä8\"	8sã8‚<@öÉ„Ïl2\$Sh±†®\nŒR\"UÏ43FN…´7\"D\r‰4˙OI3¬ò\n\0û\n`®``é≥‚ Y2 obÒ3ÛÀ<n6ì]<`êÏ\"í” Nà\"B2‡Z\nà¸m•†‡E¿ÉÎÓÈ\0£¸‡Zx¿[2¬@,¬íí˜<P›?Ù\r‘8#d<@∞¥JUä¨K/E°;\$´6ÛÃSîDU	l;§,UœLŒíÒ7fcG\"EGÄÛ\$£®\"EÄŸ3FH∆§IìÃ„dë=e	!“UH–ë23&jä»¨”*˙¬%%”%2ì,å”JQ1HÃl0tY3ˆ¡\$X<Cƒt‡4Î_\$\0©„>/Fê\nÁ¢?mF¨j÷3•p´D·ÑHKúv »∫…úé\0X‚*\r öÂ—\n0üëe\nŒ%Ôú∫‰¡\ri˚ƒÍOÄ√flâNˆ©M%]U¨QπQΩLÈ≠-Ü˜S¬±T4–!†‰U5çT\nnòdi0#àEä™M£à≥´iê.™∞/U†∏È\rZFö˙”jÑÆ®;¢ÚÌHœ‚òéd`m§›©˙ñ–\n˝tÑÉQS	eÈ≤≥|Ÿi≤öÒ¨¡Qt¶ dÚ12,õˆ¡DYÚ1UQSU¨±cd±´µƒEà)\\´ñ∂¬Lˆ	ÏF\$∂@ˆÂ≥VÔ{W6\"LlTƒÎAÚ\$6ab„ãO‰ÍdrÃ…LpÜc,í®esŒû®<2Ï`∆@bÄXP\$3‡‡êåé@ÀÉP,˙KÕV’≠^ıæ‡œMîáLˆ∞∏uÈ1˛Ÿ@Ócïàt-‰(†∏†`\0Ç9∂nÔÁ2sbÑ° / –Fm‰)∂ÙÉ¥ˇHl5Û@œnÃl\$áq+:Æ¬/ §¯ßdåœ,Ú‡\nÄﬁµàéÏÑ£.4˙ñí\$ ≥w0\$Äd∑V0†»¥\"æ√rÏˆW4678ÌVtqBau˜p√ÄäI<\$#≈x`…wdê9◊^*kÉu◊ofBEp	g2≥ÕÛf4 ‡âL!Ír=¨\0ßÒ\"	⁄\r<Í’hˆ”“ÊˆéàUÖ%T”hÀÎBkÚ∫#>≈'C•p\n†§	(Ç\r¥˙2ˆéá¬\"3‚ãlïıM‘ã7˝G≈x.à,÷Uuÿ%Dt¯ √w∂y^≠Mf\" ÇäÅÉﬁ(vUÑ3Ñu¨£J^HC_IUñYkSÖóác_ylcÜc]rF˜Â◊_q§%ÜW#]@Àr≤kv◊3-„cyƒœVHJG<ÄZ•ˆTË@V∏8ú\$é6áoÉ2H@ò\r„Ç‰¬™\0¬à=ÿ›çˆ∑Êπ\"3ã9zı≤:Kıè˙¬uØèK >Ç¢åøB\$¬r›.‰J“Í<KıG~‡PøX¥ÄQM∆π	Xåâw\$; ÊmpîZpïè ÂcK!OeOO∏?ÔwpÊƒÊá§ÌÜ÷†¶⁄Ló∂I\nåï?9xB§.]O:VÆÑòﬂ9ﬂ√.≈mWä\0Àós>î*¥l'è´ık≠∆oçphªíËxºãã´ﬁv¥L`wê1î˜∞ ÄË!∏M®4\"ÚI\$’˜\"oı\$¿†>ÀôBea\"ôÒüDˇBoÉ ∂¸+Ï B0Pxpä´&‡·7√|p{|∑œ}7÷∞¬\$-P£âÇÈ˙@bÑÖ§ıe§∆Â VYmoMùoä\0¢ß£Nzn*>›ŒÑÄ)¢Ú∑»à◊-Hál!Æìçºhp∆gŸÀ äíº€&tZ¯„ú§\0ç!Ç¶8 …©∏®‡∫ZKäÍ@DZGÖåïü∫Æ¯Ê∂FÄÁß©.Ü àºl¢¸z%»Œ(‰∂xŸ}≠˙'<ö˝≈™(∞º•˙∞Í<⁄XZ«¨∫⁄—ö‡∞ …Æg¥∫Ìß∫ÚáÚwØ∫z‘z{∞e∏'{;@Âô±(&¯≤≈R‡^EË›õx∫ÂÆõYÆÒ\"ÀÃÎ•M‹íÁÁñVˆ⁄\nß5”zl•zr‘[xü≤À™í•˙ìªG\$O†W†@§Ω¿´Zπx«Œ’ƒÚ≠,Ãïîèbeªâ 	àf£d∆ª–2˚’E√ãçãIºDëYTŸ%êkö{ŒJ≠\\\r∫U N ≈'º_æ€…Ωªf|wﬁµê˚‡À,Ωl´7™kt¯1RéD>ˆ–ãXâZÓÕ–ä≠|y|Z{|◊’¢»Ó\róÈ%;¨#\0eZ,\rKt\r∂>„ﬁ>\$Ú>ÉÏ?Ñ?c˙?‰+Ä‰@ÑÚ• Ä∆„@ ∞ïå„Çc„qàfc∆“+«3»òÉàíÿÄ&xï]ÄN∑–ˆ*|»’b2<lnTÂ÷\$£AÃ˚¢Z0.‡∆&¸ﬂÀ∑ˆº`{Àp,Ï@¸¯&|ÌïÓœñ.““.oo¢ç@ÉŒ€‰1=\$9{º…dB;øìı◊î#∆:£’\$@w“£ÿ=‹˘ÀC?– ’(˝?”É÷ ŸG1Ü|¯\"]”\0 ¸»5˚\0Ej\r¿÷@@*¢2KL∫#d*†‰CA–3,K`Ê ÿ˝¢´C±Ÿœ≠⁄§€¸˜∆‡]Ÿ„\r⁄L9€ù∞ì=¬ì<ñ∑]∏(‘jC¯) Ì,‚Á⁄Bf\r‡⁄‰ Î£-êRd5„ˆ\$\0^ê\n4§\0œ⁄¢ä≠SY›‹ÜÜkÇÄŒ4˝Ë@§B\0Á…¿Wﬂ‚?x(É¸úu}Ω‹⁄†ø‰Ω≈› K~P\rùπÂ•/‡æE\"Ωø€#È·>Rû_ÁÙ‚∏\$< ¢Ã\r«l‡[‡âæø*÷`é\n†áËÌ~¡Ωb‹˘Ω]ÅÇ›j∑B\rΩqÀ£QÍæº+˝(¸W|‡ËÂ+äep9—j}R<¥w@ÇÁ…dbÃ¥É’Ë ¿Q’§äÇÕÄ¬/(Á®¶m‘ëI_‘}U<‡›’∏«–óByÅ—˜∏Û§_Òf•&FÕå¡∑F.} zhÁ¿yó©πFcÊÜ‘œrU€´Fqõ≥û:í\nÄ‰\n%«ŒÔ`Áñ–D@Ú≥{¢Ùàñ’ﬂÒâˆˇs/wh]Bz\"J¡û#‡„àÉf¿Ä…˙˚€TCì•ç˛†_≤ÔÉdZÿ†ˆ÷£m2n¥nCíËK„ßG\\9(ÎBÜoù´ ÀÖS¸#ê‚Ü|¿£ôd)EÛëﬁÄƒ|√Î,ÄÄbg 1éN?vÌ@,‰«(\"%PLÛŸ¿˘•*B *`ÿ¿4∑+í∂X(¿â„Òa[ÙKï\0¥öƒµå?Ä“ôú*?4D\0ù◊»z,¬B–t›2_@¯c97¢~†j¡Q@Ñ∂Ë«\$VãTêô0f	P-—Ó‡∏A9+ÖÅp∞ï‡‘çüIk•O!ƒñEâÅb„H∏®÷(`fÛ,h¢ËHˆÉÉÈàAb`Ií£Ê¡·r\0na†@B!4G0x¨\"W8Yd0f,)AŸe∂Ä4àé\"v¯Ã)D81mØ¬®4Ã òte‰N@tÉ—i ˙Ë@Å§0±g\$*ê·Ç!¬<GPç\"ë\\`Ñ\$ñ3H0È~#:lªÿWûÕñA-#ò&ƒµÀ/InMäIrDH(œd¡Õk	`‰–‡áAçŸ˝<˙∆JÓg”ùƒô|@∫Dí∏’.HhœV÷ó∫–÷öKêé÷É5ôŒ!k·ÀÇ–«Wõ K!®C£p^‹\$èìı∏≠5(Ûr!“@#jD,Å+*G8@ÜG˝5>#O´:|?ö50ÈDÙ»m#\n—hjÜﬂ Í!«6<Œ\r.å∑âúúóÜ	DàÑr@‘	ägà9˘™í:ÿóêÏ‘‰;<úOÇ2z•(FK K@‘“3\nx6AÖx~@∏—ƒåF1R	˙LQu%=KJ\\TP_Ä4l ]˙z\0∫uIHK±1I‰\\\"Â@âv.„;aYMHÑ®eC	\$à\"-:8…¬Eê&\$	`E‡Ääü0,B\$)ËF¯\0Q∞¢∆F2hi0óFàTπB`\$Ä8	CŒ3¯ìvI}å0¿%–âD™'Æÿ\n\"·áΩXKc/W‚¬à±§ËùÑ{#0@Ïwøùf¡fTﬁ∞‚ë9]êﬁÜª@>%ﬁ<!ƒ„[¿⁄¯2‰gÒ‹â‹?‚Ù≠H®é-·ÄT_√>ò¡≤˙7ºâ¸c@∂ëQÊ5ë‘	R¬ö®¡\0ıú´oê\0	à¥˚êŸDÜ9GO8ë÷cHƒÜé1q@¸nä¥¯{√‰f1Ïh…GhòƒGö2dbc!®˛ú∆C§ñá‰?RÄØÄ§ó∆SY¿NZiíëüXS◊©uå§6)ﬁ·XÌ(èú&†bp¯çXbÅ*ë™¶B©ÕTm1ΩÄÜ\rÍ¨åEDÕRË∆¡Xh¥vê¸OLy’ê“Óat“ºÿpS\0¢¶Ω¯ÙaAºù™Ω%ö\\Ldò@√5Äπh+”≈VNE•Å°jKT	î°YPoñÛú\róK›)¸!⁄·'8gπäNÕ”öÉBä›4>6}É2S¸¶] À∂ÈÆ]!ÒÓí¥\0Ñrp/0ÀÑAé®«`\"àñ‰@EMùƒ∫∆6Åâ~¢Ω[ÄQ\0¬/äLQ∑V†g\0W-0>EÓZ¿bç8£RÅ!9Åº\nß MÉ-xJ¬ê–p–Y `\"áP‡P∞ñYqÑ∂îØÅÄ◊``^Ã™âI fÖ	ÀçLvÛUBéôòAW\0–£-∫#∆|‡áÊgGœåÙ3‡[jL]—ñûÇUA¢=»«\n\$ﬁ0àÆ=·òC\nﬂF±¿Îû,‘ -h≈ìÒr\0∏e‡`DŒÖ‰1ò‰é3Û1à€\nY|∏Ì†Á{ ®ÜC3êÃ<B¢ñ‘»Å4πîÖÊ0êîó¿∏Êu/°húTO¨;qúƒÅ2B°DPq†BlÿøEÕdVõÑ}\"‚!%°8pÅ®ò„-ÖÀf”≠|l›\$ﬂ«	8qäûpâHäËáH:f[L»Ä¨ €\n´9Ÿ•&[GVñ†Fg.8A0%òÚ¬ñ eå\"…√4Ñí1åÏË•Ç)“Œù2Ú1ùKRë15Ä¬K‘\rƒâô@a\nUi∂`¶≥Ωr¶Fl8‡≥∏\r[RLÚ˜c8æeªS\0(°ñ¥e¿ ƒ-Ùôº›¢∞.„\\»ËÄ.fÿ\n@x±œh¶ÀûT_ç∂/àøM‡ÌZú¬ç3?ùÆ7sÙ`X	ﬁ¢#?øxY¢Äû”˘<∫£..¥–.√&Lπ1!?≠±•—4iÄoƒÑQ\nTk≠3˙Ó\"fx\náäÇÌ3ë3qä=A®£ŒVÇ 25)ﬁ(5B—öMtoÄ(6–@rË∫6¿∂xM\rÑq¡#O&rÔ	 LÚíK˙Æ∏ŸttI•dÌA™P&Çt àÑù¢ä(~q¥&4Ì\n\"/z–¿¶!¿2ê„≤ñ‡À«xàDÖ¢»–ÿ2Å¶P≠˛dÓáÂM¿∫ƒíÜ&¨f»π(kCzãŒágî5¥g“æ‘#9«D\r°€„jœ«3Ù—ÜèS∂£≥ˇëFZBôî`_§pù¢CGö;“RéCC\\O\$>5:õ¿˜Ôwp§ıR-âÅ“•R@}\n°≤—ûX†√´,E„(C®:√G¢˛à”;/ß∏—‰ƒR¨\$ä˘d¿¨\$úQJXO@!“ù•°K·Ë|˛69¥µºØBA8ÿ§∆ztœH£úe≥.œhY'oã‰≈B]>°„∏K(*I˝PÔÜ}Q®¨Û>J,ŸÄH-ùæcÕ37⁄>ç˙»∞ÿ≤ÏÌÎŒ4A@®\n9“É¨˘v?5hBäZ`ΩÜÇú3m&*É»∏I”êıf}4àŒ†—d∞éddü¥&R±nÃ<”V‹|D\\‘ö?§-6ût8\r#BëÊLàCI;°ÕO&Wô<>¥≥\\]“0\$Òr1PÄbVõ#–t™é‘°\rGY3(™ßUT0tÄÀVåÌ¬@ËÚÑñ™Ë‹™»Üê&–7U†√ïkU™≈^ê°§∞epão´m¬ı04Ö3;≈R¶7I™N6ÿ¸uNêu_–‹†8*—Oõ3ùIÍáOç\0útùòÛˇ™4¢zRòìUH}efcˇï	Ãx≥oÙ	ÂTj∫´üUú\rµiµXê;Wi#>\0!I:πP!◊Tö”7!n-jéÏÿCºPÃ\\†|ÿÄﬁè’œµ	êç>⁄H—°¸EïtUn\$VıpµÑ'UiÇıŸ∏√◊;∏N’⁄'T2÷:∞\"≠Õx+xó∫“Vº¿n¨K‡@À^∂“õ‡oí˙´wÜ[∞©>ÃπTÆ:ZÜV÷∑‘-TNÆ®)øÃVyJ∏U ≠U∂FÂMÇ≥b¿å∑H¡E±ÊñZy⁄ªú∫`ùt0ÂŸf=@÷»≥\0†¿E¢˜2÷Ñ∆I‘ñ0tÆ>-	ùÚ\$1∏¸i™Z“”MHLî2rp¥ƒ¿+|VKß≤◊¬õd˜fYNA€`õYrÈÀL†ÜÜ®êüyQZãö4\0001±íÍ&YPö‘Y#ÂæméõIMyÈ3∆¡ñCYMf#óS≤Õî∑ìxá÷R≠K6L∏œf“∏ïdK,ºlEìà¥k7\rv˛k*ãåˆX\nmîÌ(∂§-çŸÇ–≤ò\nÏ≥eµrL\"Ã∂g≥àQCâgK5ÿÓ’uµlèÏ’\$†≤»ƒ\n`hû¿˙ÁWƒøpkÀ†ô]C≈0Õò†ÙLâ´¢Ö•SòrTÒ˝¥_â™môBh®Ä5ß∂Îë^·ı◊÷¡‚ÇË¡Ïæ`§∂Q±)\rDƒ°e\"«/±\0ÌAÜÖ‡%#ßBÑä&v∆='Ré&JR√XÍ÷ªû¬D@XcGâË\"¨Ï\rxJÊ%R◊\\- ¡ÛC\0^  Å9>`|åä|\$\$Cd¶—<Bÿ	≈ip∆E‚¿5…π=ƒ¿›qZÎÅ‚tF\r\0¢ux>@yÓá9≠ø.Z'êøñ‡Ì≠œ√_s\0002€äÊÚ˛n’—Ç\rM‡\\@T†Y†Ë%Ågû¢	‡4}°VXJS‚’Ë4X÷ÆÑ˘IMæX‡‰µ¶Qö)KF¥X°mtı©—1våô‚Ì¥Ã!„ÙAuG]X)%Ó~¯≥éﬂ@i\$≈\$FÙ∂ºD(GE∏ät”ˆ‘6\r(bÿ3Tê…ë∆ÄMzlTQàûP˚@˚vIãøëñ≈≤˚.Ó÷÷â äûÓ◊3ùﬁM\rw∑W““Ä\\û]‡M	zã√›m“ë04}„OB(õﬁAÙóìK¸/+'ãÁﬁj”#|ΩàÕlÕ≥u˙ÙLΩŒ;D+”Q^Òó<<M¢‹KhE¶Ê’∂£W§Ò~ÎÈ-x\\w’æπ∫EÈ}•ˆö¶ÏA_æúÔTd\\JjÃΩyÚßí*xÅ»lË˜©F(Ì®vrË·P†7Üøı∂mó~F‡ﬂ“ƒ;±®Ü{7¡%Ãº£—¬™Àò3aÈE\0që˙ö0>G≠¿ü%˛3¬ÊnÈ≠Äg`*5ÿ@∂3ŸèMlﬁX\rH\0WÇ5º8Å‚ñåªB}YÎ`(˙Wá¬&Ê{á\n_W21¿atpägk≈\rÆ&Ü¡±û/„GÿØÄªÇ–¡Ù«gq\$ù\\”¯qΩÄfÈÙü]¥¯1N9%Ô\\G@ŸÑ#=|J:µÔÕ|å[l€xx!›Ù}Üz{≈~˛¶∫øªl∞È}Í õ}¥Wû\\åq\rl⁄'_¢ãE/6ÍY± µ¸I£ÜºøƒùÔ˜”0?í€Wh¿Ké≠f31è%r”mX`\0XÄÊWÃÒ†∫“—2#@X|Áœä.d.†üá‡h—¯i‘Äîï\n°ûSAóNEÑ»≈¶0Ò7\0wQlo©u3%‹£·pêÈãÎ\\	|m\0∞ZÅ@–\0Ï`î)¨Ä!§‹	·E´é¨p\0∂á`/»MêWxÆãh`»``y\"™„kˆÿ<.lË¶≈Gâ<ñ	X~y'˘›Æ»®∏≠˛r„¬ÆµÀˆ˚e°Éã¢ÒìÃd†hew\natT@¸õã‹ÒD	Ñ∫a.DPœP≈\"=´7ì°ù≥∞!†]¯€2ùî6§1⁄ø‡: xóqK|qÑñ˘D\\^Diê0:\0ƒØÔ\0ér∞3º¢Ÿ0≈—tœF^Ÿù©∂Y';ÈQûHò≥(ì·IñZ2‘¥04%ü	pô.å_¿[ñÄøø-ˆm1∆ZﬁaÜ|< ÜF‚!ë‹EÓÃ≤ãK&cÛ1ô†6º,Ê]nb3}(LÂ†πçãûdVµò2Ê[-\0002ÕFg2ΩôÏ\rfÅ—Ÿ£16iswõ’¬î]+äa˙rÎ:X≤ﬂX	S-3'óÅxŸ€À¨œ≤ÒiahD\rîˇºMS—ñ§ìG3πa¶XÚ5ƒÙ.∑=9Åœf{≤-ó√TÂ¸Ú9øƒÎ≥‚^\\∆õnË)Ë›ù3Ä9Ã·¬Ã¢Î{0s\\83‰\$ò:Ç˝3ê›f‰√‰Ô0¯\nÛÊß@©\\Ã†arö·ü<‚Éœéà37¢0ΩÁ0\"Ôá˝*25°¨Çe£9Ω¢rûùôÎ¯Ú2\0 ª\"GÖ¬YHò—Ê>±˘êá 9V-L«25uÎ]ÄwzXO‹;¿∂H¸ÉÜ\\ -»†x\$áêlâd'!a‡^è1˜h'îiÜ˘i£G6Àd{ç∞“—ZC>Ê∏>≈ `Crú©oÄ©+u@êı∑PœíP!ÄÃN†‘Ø•A j\".zä‘f£uzaä	Ä,`+◊åÌK™]ôrÓt≠äÀGM„÷Nd/£û˙ÊvkŸ2π†=ã§‚√ 1¿ﬁÖ®<NE+µ4à—I¿-ÅÇìò\0fˆ–>Ä");}else{header("Content-Type: image/gif");switch($_GET["file"]){case"plus.gif":echo"GIF87a\0\0°\0\0ÓÓÓ\0\0\0ôôô\0\0\0,\0\0\0\0\0\0\0!Ñè©ÀÌMÒÃ*)æo˙Ø) qï°eàµÓ#ƒÚLÀ\0;";break;case"cross.gif":echo"GIF87a\0\0°\0\0ÓÓÓ\0\0\0ôôô\0\0\0,\0\0\0\0\0\0\0#Ñè©ÀÌ#\na÷Fo~y√.Å_waî·1Á±JÓG¬L◊6]\0\0;";break;case"up.gif":echo"GIF87a\0\0°\0\0ÓÓÓ\0\0\0ôôô\0\0\0,\0\0\0\0\0\0\0 Ñè©ÀÌMQN\nÔ}Ùûa8äyöa≈∂Æ\0«Ú\0;";break;case"down.gif":echo"GIF87a\0\0°\0\0ÓÓÓ\0\0\0ôôô\0\0\0,\0\0\0\0\0\0\0 Ñè©ÀÌMÒÃ*)æ[W˛\\¢«L&Ÿú∆∂ï\0«Ú\0;";break;case"arrow.gif":echo"GIF89a\0\n\0Ä\0\0ÄÄÄˇˇˇ!˘\0\0\0,\0\0\0\0\0\n\0\0Çiñ±ãûî™”≤ﬁª\0\0;";break;}}exit;}function
connection(){global$h;return$h;}function
adminer(){global$b;return$b;}function
idf_unescape($s){$id=substr($s,-1);return
str_replace($id.$id,$id,substr($s,1,-1));}function
escape_string($W){return
substr(q($W),1,-1);}function
remove_slashes($Qe,$mc=false){if(get_magic_quotes_gpc()){while(list($w,$W)=each($Qe)){foreach($W
as$bd=>$V){unset($Qe[$w][$bd]);if(is_array($V)){$Qe[$w][stripslashes($bd)]=$V;$Qe[]=&$Qe[$w][stripslashes($bd)];}else$Qe[$w][stripslashes($bd)]=($mc?$V:stripslashes($V));}}}}function
bracket_escape($s,$Aa=false){static$ng=array(':'=>':1',']'=>':2','['=>':3');return
strtr($s,($Aa?array_flip($ng):$ng));}function
h($N){return
htmlspecialchars(str_replace("\0","",$N),ENT_QUOTES);}function
nbsp($N){return(trim($N)!=""?h($N):"&nbsp;");}function
nl_br($N){return
str_replace("\n","<br>",$N);}function
checkbox($A,$X,$Na,$gd="",$ce="",$ad=false){static$r=0;$r++;$H="<input type='checkbox' name='$A' value='".h($X)."'".($Na?" checked":"").($ce?' onclick="'.h($ce).'"':'').($ad?" class='jsonly'":"")." id='checkbox-$r'>";return($gd!=""?"<label for='checkbox-$r'>$H".h($gd)."</label>":$H);}function
optionlist($ge,$uf=null,$Gg=false){$H="";foreach($ge
as$bd=>$V){$he=array($bd=>$V);if(is_array($V)){$H.='<optgroup label="'.h($bd).'">';$he=$V;}foreach($he
as$w=>$W)$H.='<option'.($Gg||is_string($w)?' value="'.h($w).'"':'').(($Gg||is_string($w)?(string)$w:$W)===$uf?' selected':'').'>'.h($W);if(is_array($V))$H.='</optgroup>';}return$H;}function
html_select($A,$ge,$X="",$be=true){if($be)return"<select name='".h($A)."'".(is_string($be)?' onchange="'.h($be).'"':"").">".optionlist($ge,$X)."</select>";$H="";foreach($ge
as$w=>$W)$H.="<label><input type='radio' name='".h($A)."' value='".h($w)."'".($w==$X?" checked":"").">".h($W)."</label>";return$H;}function
confirm($gb=""){return" onclick=\"return confirm('".'Are you sure?'.($gb?" (' + $gb + ')":"")."');\"";}function
print_fieldset($r,$nd,$Og=false,$ce=""){echo"<fieldset><legend><a href='#fieldset-$r' onclick=\"".h($ce)."return !toggle('fieldset-$r');\">$nd</a></legend><div id='fieldset-$r'".($Og?"":" class='hidden'").">\n";}function
bold($Ha){return($Ha?" class='active'":"");}function
odd($H=' class="odd"'){static$q=0;if(!$H)$q=-1;return($q++%2?$H:'');}function
js_escape($N){return
addcslashes($N,"\r\n'\\/");}function
json_row($w,$W=null){static$nc=true;if($nc)echo"{";if($w!=""){echo($nc?"":",")."\n\t\"".addcslashes($w,"\r\n\"\\").'": '.($W!==null?'"'.addcslashes($W,"\r\n\"\\").'"':'undefined');$nc=false;}else{echo"\n}\n";$nc=true;}}function
ini_bool($Qc){$W=ini_get($Qc);return(eregi('^(on|true|yes)$',$W)||(int)$W);}function
sid(){static$H;if($H===null)$H=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$H;}function
q($N){global$h;return$h->quote($N);}function
get_vals($F,$f=0){global$h;$H=array();$G=$h->query($F);if(is_object($G)){while($I=$G->fetch_row())$H[]=$I[$f];}return$H;}function
get_key_vals($F,$i=null){global$h;if(!is_object($i))$i=$h;$H=array();$G=$i->query($F);if(is_object($G)){while($I=$G->fetch_row())$H[$I[0]]=$I[1];}return$H;}function
get_rows($F,$i=null,$l="<p class='error'>"){global$h;$bb=(is_object($i)?$i:$h);$H=array();$G=$bb->query($F);if(is_object($G)){while($I=$G->fetch_assoc())$H[]=$I;}elseif(!$G&&!is_object($i)&&$l&&defined("PAGE_HEADER"))echo$l.error()."\n";return$H;}function
unique_array($I,$u){foreach($u
as$t){if(ereg("PRIMARY|UNIQUE",$t["type"])){$H=array();foreach($t["columns"]as$w){if(!isset($I[$w]))continue
2;$H[$w]=$I[$w];}return$H;}}}function
where($Z,$n=array()){global$v;$H=array();$yc='(^[\w\(]+'.str_replace("_",".*",preg_quote(idf_escape("_"))).'\)+$)';foreach((array)$Z["where"]as$w=>$W){$w=bracket_escape($w,1);$H[]=(preg_match($yc,$w)?$w:idf_escape($w)).(($v=="sql"&&ereg('\\.',$W))||$v=="mssql"?" LIKE ".exact_value(addcslashes($W,"%_\\")):" = ".unconvert_field($n[$w],exact_value($W)));}foreach((array)$Z["null"]as$w)$H[]=idf_escape($w)." IS NULL";return
implode(" AND ",$H);}function
where_check($W,$n=array()){parse_str($W,$Ma);remove_slashes(array(&$Ma));return
where($Ma,$n);}function
where_link($q,$f,$X,$de="="){return"&where%5B$q%5D%5Bcol%5D=".urlencode($f)."&where%5B$q%5D%5Bop%5D=".urlencode(($X!==null?$de:"IS NULL"))."&where%5B$q%5D%5Bval%5D=".urlencode($X);}function
convert_fields($g,$n,$K=array()){$H="";foreach($g
as$w=>$W){if($K&&!in_array(idf_escape($w),$K))continue;$wa=convert_field($n[$w]);if($wa)$H.=", $wa AS ".idf_escape($w);}return$H;}function
cookie($A,$X){global$ba;$ve=array($A,(ereg("\n",$X)?"":$X),time()+2592000,preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]),"",$ba);if(version_compare(PHP_VERSION,'5.2.0')>=0)$ve[]=true;return
call_user_func_array('setcookie',$ve);}function
restart_session(){if(!ini_bool("session.use_cookies"))session_start();}function
stop_session(){if(!ini_bool("session.use_cookies"))session_write_close();}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$W){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($_b,$L,$U,$k=null){global$Ab;preg_match('~([^?]*)\\??(.*)~',remove_from_uri(implode("|",array_keys($Ab))."|username|".($k!==null?"db|":"").session_name()),$_);return"$_[1]?".(sid()?SID."&":"").($_b!="server"||$L!=""?urlencode($_b)."=".urlencode($L)."&":"")."username=".urlencode($U).($k!=""?"&db=".urlencode($k):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($z,$Cd=null){if($Cd!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($z!==null?$z:$_SERVER["REQUEST_URI"]))][]=$Cd;}if($z!==null){if($z=="")$z=".";header("Location: $z");exit;}}function
query_redirect($F,$z,$Cd,$We=true,$Zb=true,$gc=false){global$h,$l,$b;$dg="";if($Zb){$Ef=microtime();$gc=!$h->query($F);$dg="; -- ".format_time($Ef,microtime());}$Df="";if($F)$Df=$b->messageQuery($F.$dg);if($gc){$l=error().$Df;return
false;}if($We)redirect($z,$Cd.$Df);return
true;}function
queries($F=null){global$h;static$Te=array();if($F===null)return
implode("\n",$Te);$Ef=microtime();$H=$h->query($F);$Te[]=(ereg(';$',$F)?"DELIMITER ;;\n$F;\nDELIMITER ":$F)."; -- ".format_time($Ef,microtime());return$H;}function
apply_queries($F,$Q,$Ub='table'){foreach($Q
as$O){if(!queries("$F ".$Ub($O)))return
false;}return
true;}function
queries_redirect($z,$Cd,$We){return
query_redirect(queries(),$z,$Cd,$We,false,!$We);}function
format_time($Ef,$Ob){return
sprintf('%.3f s',max(0,array_sum(explode(" ",$Ob))-array_sum(explode(" ",$Ef))));}function
remove_from_uri($ue=""){return
substr(preg_replace("~(?<=[?&])($ue".(SID?"":"|".session_name()).")=[^&]*&~",'',"$_SERVER[REQUEST_URI]&"),0,-1);}function
pagination($C,$lb){return" ".($C==$lb?$C+1:'<a href="'.h(remove_from_uri("page").($C?"&page=$C":"")).'">'.($C+1)."</a>");}function
get_file($w,$rb=false){$kc=$_FILES[$w];if(!$kc)return
null;foreach($kc
as$w=>$W)$kc[$w]=(array)$W;$H='';foreach($kc["error"]as$w=>$l){if($l)return$l;$A=$kc["name"][$w];$kg=$kc["tmp_name"][$w];$cb=file_get_contents($rb&&ereg('\\.gz$',$A)?"compress.zlib://$kg":$kg);if($rb){$Ef=substr($cb,0,3);if(function_exists("iconv")&&ereg("^\xFE\xFF|^\xFF\xFE",$Ef,$df))$cb=iconv("utf-16","utf-8",$cb);elseif($Ef=="\xEF\xBB\xBF")$cb=substr($cb,3);}$H.=$cb."\n\n";}return$H;}function
upload_error($l){$_d=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'Unable to upload a file.'.($_d?" ".sprintf('Maximum allowed file size is %sB.',$_d):""):'File does not exist.');}function
repeat_pattern($Be,$od){return
str_repeat("$Be{0,65535}",$od/65535)."$Be{0,".($od%65535)."}";}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\\0-\\x8\\xB\\xC\\xE-\\x1F]~',$W));}function
shorten_utf8($N,$od=80,$Lf=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{FFFF}]",$od).")($)?)u",$N,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$od).")($)?)",$N,$_);return
h($_[1]).$Lf.(isset($_[2])?"":"<i>...</i>");}function
friendly_url($W){return
preg_replace('~[^a-z0-9_]~i','-',$W);}function
hidden_fields($Qe,$Lc=array()){while(list($w,$W)=each($Qe)){if(is_array($W)){foreach($W
as$bd=>$V)$Qe[$w."[$bd]"]=$V;}elseif(!in_array($w,$Lc))echo'<input type="hidden" name="'.h($w).'" value="'.h($W).'">';}}function
hidden_fields_get(){echo(sid()?'<input type="hidden" name="'.session_name().'" value="'.h(session_id()).'">':''),(SERVER!==null?'<input type="hidden" name="'.DRIVER.'" value="'.h(SERVER).'">':""),'<input type="hidden" name="username" value="'.h($_GET["username"]).'">';}function
column_foreign_keys($O){global$b;$H=array();foreach($b->foreignKeys($O)as$o){foreach($o["source"]as$W)$H[$W][]=$o;}return$H;}function
enum_input($S,$ya,$m,$X,$Nb=null){global$b;preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$vd);$H=($Nb!==null?"<label><input type='$S'$ya value='$Nb'".((is_array($X)?in_array($Nb,$X):$X===0)?" checked":"")."><i>".'empty'."</i></label>":"");foreach($vd[1]as$q=>$W){$W=stripcslashes(str_replace("''","'",$W));$Na=(is_int($X)?$X==$q+1:(is_array($X)?in_array($q+1,$X):$X===$W));$H.=" <label><input type='$S'$ya value='".($q+1)."'".($Na?' checked':'').'>'.h($b->editVal($W,$m)).'</label>';}return$H;}function
input($m,$X,$p){global$h,$T,$b,$v;$A=h(bracket_escape($m["field"]));echo"<td class='function'>";$ff=($v=="mssql"&&$m["auto_increment"]);if($ff&&!$_POST["save"])$p=null;$zc=(isset($_GET["select"])||$ff?array("orig"=>'original'):array())+$b->editFunctions($m);$ya=" name='fields[$A]'";if($m["type"]=="enum")echo
nbsp($zc[""])."<td>".$b->editInput($_GET["edit"],$m,$ya,$X);else{$nc=0;foreach($zc
as$w=>$W){if($w===""||!$W)break;$nc++;}$be=($nc?" onchange=\"var f = this.form['function[".h(js_escape(bracket_escape($m["field"])))."]']; if ($nc > f.selectedIndex) f.selectedIndex = $nc;\"":"");$ya.=$be;echo(count($zc)>1?html_select("function[$A]",$zc,$p===null||in_array($p,$zc)||isset($zc[$p])?$p:"","functionChange(this);"):nbsp(reset($zc))).'<td>';$Sc=$b->editInput($_GET["edit"],$m,$ya,$X);if($Sc!="")echo$Sc;elseif($m["type"]=="set"){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$vd);foreach($vd[1]as$q=>$W){$W=stripcslashes(str_replace("''","'",$W));$Na=(is_int($X)?($X>>$q)&1:in_array($W,explode(",",$X),true));echo" <label><input type='checkbox' name='fields[$A][$q]' value='".(1<<$q)."'".($Na?' checked':'')."$be>".h($b->editVal($W,$m)).'</label>';}}elseif(ereg('blob|bytea|raw|file',$m["type"])&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$A'$be>";elseif(($bg=ereg('text|lob',$m["type"]))||ereg("\n",$X)){if($bg&&$v!="sqlite")$ya.=" cols='50' rows='12'";else{$J=min(12,substr_count($X,"\n")+1);$ya.=" cols='30' rows='$J'".($J==1?" style='height: 1.2em;'":"");}echo"<textarea$ya>".h($X).'</textarea>';}else{$Bd=(!ereg('int',$m["type"])&&preg_match('~^(\\d+)(,(\\d+))?$~',$m["length"],$_)?((ereg("binary",$m["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$m["unsigned"]?1:0)):($T[$m["type"]]?$T[$m["type"]]+($m["unsigned"]?0:1):0));if($h->server_info>=5.6&&ereg('time',$m["type"]))$Bd+=7;echo"<input".(ereg('int',$m["type"])?" type='number'":"")." value='".h($X)."'".($Bd?" maxlength='$Bd'":"").(ereg('char|binary',$m["type"])&&$Bd>20?" size='40'":"")."$ya>";}}}function
process_input($m){global$b;$s=bracket_escape($m["field"]);$p=$_POST["function"][$s];$X=$_POST["fields"][$s];if($m["type"]=="enum"){if($X==-1)return
false;if($X=="")return"NULL";return+$X;}if($m["auto_increment"]&&$X=="")return
null;if($p=="orig")return($m["on_update"]=="CURRENT_TIMESTAMP"?idf_escape($m["field"]):false);if($p=="NULL")return"NULL";if($m["type"]=="set")return
array_sum((array)$X);if(ereg('blob|bytea|raw|file',$m["type"])&&ini_bool("file_uploads")){$kc=get_file("fields-$s");if(!is_string($kc))return
false;return
q($kc);}return$b->processInput($m,$X,$p);}function
search_tables(){global$b,$h;$_GET["where"][0]["op"]="LIKE %%";$_GET["where"][0]["val"]=$_POST["query"];$tc=false;foreach(table_status('',true)as$O=>$P){$A=$b->tableName($P);if(isset($P["Engine"])&&$A!=""&&(!$_POST["tables"]||in_array($O,$_POST["tables"]))){$G=$h->query("SELECT".limit("1 FROM ".table($O)," WHERE ".implode(" AND ",$b->selectSearchProcess(fields($O),array())),1));if(!$G||$G->fetch_row()){if(!$tc){echo"<ul>\n";$tc=true;}echo"<li>".($G?"<a href='".h(ME."select=".urlencode($O)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$A</a>\n":"$A: <span class='error'>".error()."</span>\n");}}}echo($tc?"</ul>":"<p class='message'>".'No tables.')."\n";}function
dump_headers($Kc,$Kd=false){global$b;$H=$b->dumpHeaders($Kc,$Kd);$se=$_POST["output"];if($se!="text")header("Content-Disposition: attachment; filename=".$b->dumpFilename($Kc).".$H".($se!="file"&&!ereg('[^0-9a-z]',$se)?".$se":""));session_write_close();ob_flush();flush();return$H;}function
dump_csv($I){foreach($I
as$w=>$W){if(preg_match("~[\"\n,;\t]~",$W)||$W==="")$I[$w]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($_POST["format"]=="tsv"?"\t":";")),$I)."\r\n";}function
apply_sql_function($p,$f){return($p?($p=="unixepoch"?"DATETIME($f, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$f)"):$f);}function
password_file(){$xb=ini_get("upload_tmp_dir");if(!$xb){if(function_exists('sys_get_temp_dir'))$xb=sys_get_temp_dir();else{$lc=@tempnam("","");if(!$lc)return
false;$xb=dirname($lc);unlink($lc);}}$lc="$xb/adminer.key";$H=@file_get_contents($lc);if($H)return$H;$vc=@fopen($lc,"w");if($vc){$H=md5(uniqid(mt_rand(),true));fwrite($vc,$H);fclose($vc);}return$H;}function
is_mail($Kb){$xa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$zb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Be="$xa+(\\.$xa+)*@($zb?\\.)+$zb";return
preg_match("(^$Be(,\\s*$Be)*\$)i",$Kb);}function
is_url($N){$zb='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return(preg_match("~^(https?)://($zb?\\.)+$zb(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$N,$_)?strtolower($_[1]):"");}function
is_shortable($m){return
ereg('char|text|lob|geometry|point|linestring|polygon',$m["type"]);}function
slow_query($F){global$b,$R;$k=$b->database();if(support("kill")&&is_object($i=connect())&&($k==""||$i->select_db($k))){$ed=$i->result("SELECT CONNECTION_ID()");echo'<script type="text/javascript">
var timeout = setTimeout(function () {
	ajax(\'',js_escape(ME),'script=kill\', function () {
	}, \'token=',$R,'&kill=',$ed,'\');
}, ',1000*$b->queryTimeout(),');
</script>
';}else$i=null;ob_flush();flush();$H=@get_key_vals($F,$i);if($i){echo"<script type='text/javascript'>clearTimeout(timeout);</script>\n";ob_flush();flush();}return
array_keys($H);}function
lzw_decompress($Ea){$wb=256;$Fa=8;$Ra=array();$gf=0;$hf=0;for($q=0;$q<strlen($Ea);$q++){$gf=($gf<<8)+ord($Ea[$q]);$hf+=8;if($hf>=$Fa){$hf-=$Fa;$Ra[]=$gf>>$hf;$gf&=(1<<$hf)-1;$wb++;if($wb>>$Fa)$Fa++;}}$vb=range("\0","\xFF");$H="";foreach($Ra
as$q=>$Qa){$Jb=$vb[$Qa];if(!isset($Jb))$Jb=$Sg.$Sg[0];$H.=$Jb;if($q)$vb[]=$Sg.$Jb[0];$Sg=$Jb;}return$H;}global$b,$h,$Ab,$Hb,$Rb,$l,$zc,$Dc,$ba,$Rc,$v,$ca,$hd,$ae,$Ce,$If,$R,$pg,$T,$Cg,$ia;if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";$ba=$_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off");@ini_set("session.use_trans_sid",false);if(!defined("SID")){session_name("adminer_sid");$ve=array(0,preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]),"",$ba);if(version_compare(PHP_VERSION,'5.2.0')>=0)$ve[]=true;call_user_func_array('session_set_cookie_params',$ve);session_start();}remove_slashes(array(&$_GET,&$_POST,&$_COOKIE),$mc);if(function_exists("set_magic_quotes_runtime"))set_magic_quotes_runtime(false);@set_time_limit(0);@ini_set("zend.ze1_compatibility_mode",false);@ini_set("precision",20);function
get_lang(){return'en';}function
lang($og,$Sd=null){if(is_array($og)){$Ee=($Sd==1?0:1);$og=$og[$Ee];}$og=str_replace("%d","%s",$og);$Sd=number_format($Sd,0,".",',');return
sprintf($og,$Sd);}if(extension_loaded('pdo')){class
Min_PDO
extends
PDO{var$_result,$server_info,$affected_rows,$errno,$error;function
__construct(){global$b;$Ee=array_search("SQL",$b->operators);if($Ee!==false)unset($b->operators[$Ee]);}function
dsn($Eb,$U,$D,$Yb='auth_error'){set_exception_handler($Yb);parent::__construct($Eb,$U,$D);restore_exception_handler();$this->setAttribute(13,array('Min_PDOStatement'));$this->server_info=$this->getAttribute(4);}function
query($F,$xg=false){$G=parent::query($F);$this->error="";if(!$G){list(,$this->errno,$this->error)=$this->errorInfo();return
false;}$this->store_result($G);return$G;}function
multi_query($F){return$this->_result=$this->query($F);}function
store_result($G=null){if(!$G){$G=$this->_result;if(!$G)return
false;}if($G->columnCount()){$G->num_rows=$G->rowCount();return$G;}$this->affected_rows=$G->rowCount();return
true;}function
next_result(){if(!$this->_result)return
false;$this->_result->_offset=0;return@$this->_result->nextRowset();}function
result($F,$m=0){$G=$this->query($F);if(!$G)return
false;$I=$G->fetch();return$I[$m];}}class
Min_PDOStatement
extends
PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch(2);}function
fetch_row(){return$this->fetch(3);}function
fetch_field(){$I=(object)$this->getColumnMeta($this->_offset++);$I->orgtable=$I->table;$I->orgname=$I->name;$I->charsetnr=(in_array("blob",(array)$I->flags)?63:0);return$I;}}}$Ab=array();$Ab["sqlite"]="SQLite 3";$Ab["sqlite2"]="SQLite 2";if(isset($_GET["sqlite"])||isset($_GET["sqlite2"])){$He=array((isset($_GET["sqlite"])?"SQLite3":"SQLite"),"PDO_SQLite");define("DRIVER",(isset($_GET["sqlite"])?"sqlite":"sqlite2"));if(class_exists(isset($_GET["sqlite"])?"SQLite3":"SQLiteDatabase")){if(isset($_GET["sqlite"])){class
Min_SQLite{var$extension="SQLite3",$server_info,$affected_rows,$errno,$error,$_link;function
Min_SQLite($lc){$this->_link=new
SQLite3($lc);$Mg=$this->_link->version();$this->server_info=$Mg["versionString"];}function
query($F){$G=@$this->_link->query($F);$this->error="";if(!$G){$this->errno=$this->_link->lastErrorCode();$this->error=$this->_link->lastErrorMsg();return
false;}elseif($G->numColumns())return
new
Min_Result($G);$this->affected_rows=$this->_link->changes();return
true;}function
quote($N){return(is_utf8($N)?"'".$this->_link->escapeString($N)."'":"x'".reset(unpack('H*',$N))."'");}function
store_result(){return$this->_result;}function
result($F,$m=0){$G=$this->query($F);if(!is_object($G))return
false;$I=$G->_result->fetchArray();return$I[$m];}}class
Min_Result{var$_result,$_offset=0,$num_rows;function
Min_Result($G){$this->_result=$G;}function
fetch_assoc(){return$this->_result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->_result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$f=$this->_offset++;$S=$this->_result->columnType($f);return(object)array("name"=>$this->_result->columnName($f),"type"=>$S,"charsetnr"=>($S==SQLITE3_BLOB?63:0),);}function
__desctruct(){return$this->_result->finalize();}}}else{class
Min_SQLite{var$extension="SQLite",$server_info,$affected_rows,$error,$_link;function
Min_SQLite($lc){$this->server_info=sqlite_libversion();$this->_link=new
SQLiteDatabase($lc);}function
query($F,$xg=false){$Hd=($xg?"unbufferedQuery":"query");$G=@$this->_link->$Hd($F,SQLITE_BOTH,$l);$this->error="";if(!$G){$this->error=$l;return
false;}elseif($G===true){$this->affected_rows=$this->changes();return
true;}return
new
Min_Result($G);}function
quote($N){return"'".sqlite_escape_string($N)."'";}function
store_result(){return$this->_result;}function
result($F,$m=0){$G=$this->query($F);if(!is_object($G))return
false;$I=$G->_result->fetch();return$I[$m];}}class
Min_Result{var$_result,$_offset=0,$num_rows;function
Min_Result($G){$this->_result=$G;if(method_exists($G,'numRows'))$this->num_rows=$G->numRows();}function
fetch_assoc(){$I=$this->_result->fetch(SQLITE_ASSOC);if(!$I)return
false;$H=array();foreach($I
as$w=>$W)$H[($w[0]=='"'?idf_unescape($w):$w)]=$W;return$H;}function
fetch_row(){return$this->_result->fetch(SQLITE_NUM);}function
fetch_field(){$A=$this->_result->fieldName($this->_offset++);$Be='(\\[.*]|"(?:[^"]|"")*"|(.+))';if(preg_match("~^($Be\\.)?$Be\$~",$A,$_)){$O=($_[3]!=""?$_[3]:idf_unescape($_[2]));$A=($_[5]!=""?$_[5]:idf_unescape($_[4]));}return(object)array("name"=>$A,"orgname"=>$A,"orgtable"=>$O,);}}}}elseif(extension_loaded("pdo_sqlite")){class
Min_SQLite
extends
Min_PDO{var$extension="PDO_SQLite";function
Min_SQLite($lc){$this->dsn(DRIVER.":$lc","","");}}}if(class_exists("Min_SQLite")){class
Min_DB
extends
Min_SQLite{function
Min_DB(){$this->Min_SQLite(":memory:");}function
select_db($lc){if(is_readable($lc)&&$this->query("ATTACH ".$this->quote(ereg("(^[/\\\\]|:)",$lc)?$lc:dirname($_SERVER["SCRIPT_FILENAME"])."/$lc")." AS a")){$this->Min_SQLite($lc);return
true;}return
false;}function
multi_query($F){return$this->_result=$this->query($F);}function
next_result(){return
false;}}}function
idf_escape($s){return'"'.str_replace('"','""',$s).'"';}function
table($s){return
idf_escape($s);}function
connect(){return
new
Min_DB;}function
get_databases(){return
array();}function
limit($F,$Z,$x,$B=0,$wf=" "){return" $F$Z".($x!==null?$wf."LIMIT $x".($B?" OFFSET $B":""):"");}function
limit1($F,$Z){global$h;return($h->result("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($F,$Z,1):" $F$Z");}function
db_collation($k,$Ua){global$h;return$h->result("PRAGMA encoding");}function
engines(){return
array();}function
logged_user(){return
get_current_user();}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view') ORDER BY (name = 'sqlite_sequence'), name",1);}function
count_tables($j){return
array();}function
table_status($A=""){global$h;$H=array();foreach(get_rows("SELECT name AS Name, type AS Engine FROM sqlite_master WHERE type IN ('table', 'view')".($A!=""?" AND name = ".q($A):""))as$I){$I["Oid"]="t";$I["Auto_increment"]="";$I["Rows"]=$h->result("SELECT COUNT(*) FROM ".idf_escape($I["Name"]));$H[$I["Name"]]=$I;}foreach(get_rows("SELECT * FROM sqlite_sequence",null,"")as$I)$H[$I["name"]]["Auto_increment"]=$I["seq"];return($A!=""?$H[$A]:$H);}function
is_view($P){return$P["Engine"]=="view";}function
fk_support($P){global$h;return!$h->result("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
fields($O){$H=array();foreach(get_rows("PRAGMA table_info(".table($O).")")as$I){$S=strtolower($I["type"]);$sb=$I["dflt_value"];$H[$I["name"]]=array("field"=>$I["name"],"type"=>(eregi("int",$S)?"integer":(eregi("char|clob|text",$S)?"text":(eregi("blob",$S)?"blob":(eregi("real|floa|doub",$S)?"real":"numeric")))),"full_type"=>$S,"default"=>(ereg("'(.*)'",$sb,$_)?str_replace("''","'",$_[1]):($sb=="NULL"?null:$sb)),"null"=>!$I["notnull"],"auto_increment"=>eregi('^integer$',$S)&&$I["pk"],"privileges"=>array("select"=>1,"insert"=>1,"update"=>1),"primary"=>$I["pk"],);}return$H;}function
indexes($O,$i=null){$H=array();$Ke=array();foreach(fields($O)as$m){if($m["primary"])$Ke[]=$m["field"];}if($Ke)$H[""]=array("type"=>"PRIMARY","columns"=>$Ke,"lengths"=>array());foreach(get_rows("PRAGMA index_list(".table($O).")")as$I){if(!ereg("^sqlite_",$I["name"])){$H[$I["name"]]["type"]=($I["unique"]?"UNIQUE":"INDEX");$H[$I["name"]]["lengths"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($I["name"]).")")as$of)$H[$I["name"]]["columns"][]=$of["name"];}}return$H;}function
foreign_keys($O){$H=array();foreach(get_rows("PRAGMA foreign_key_list(".table($O).")")as$I){$o=&$H[$I["id"]];if(!$o)$o=$I;$o["source"][]=$I["from"];$o["target"][]=$I["to"];}return$H;}function
view($A){global$h;return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\\s+~iU','',$h->result("SELECT sql FROM sqlite_master WHERE name = ".q($A))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($k){return
false;}function
error(){global$h;return
h($h->error);}function
exact_value($W){return
q($W);}function
check_sqlite_name($A){global$h;$fc="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($fc)\$~",$A)){$h->error=sprintf('Please use one of the extensions %s.',str_replace("|",", ",$fc));return
false;}return
true;}function
create_database($k,$e){global$h;if(file_exists($k)){$h->error='File exists.';return
false;}if(!check_sqlite_name($k))return
false;$y=new
Min_SQLite($k);$y->query('PRAGMA encoding = "UTF-8"');$y->query('CREATE TABLE adminer (i)');$y->query('DROP TABLE adminer');return
true;}function
drop_databases($j){global$h;$h->Min_SQLite(":memory:");foreach($j
as$k){if(!@unlink($k)){$h->error='File exists.';return
false;}}return
true;}function
rename_database($A,$e){global$h;if(!check_sqlite_name($A))return
false;$h->Min_SQLite(":memory:");$h->error='File exists.';return@rename(DB,$A);}function
auto_increment(){return" PRIMARY KEY".(DRIVER=="sqlite"?" AUTOINCREMENT":"");}function
alter_table($O,$A,$n,$pc,$Ya,$Pb,$e,$za,$ze){$Fg=($O==""||$pc);foreach($n
as$m){if($m[0]!=""||!$m[1]||$m[2]){$Fg=true;break;}}$c=array();$qe=array();$Le=false;foreach($n
as$m){if($m[1]){if($m[1][6])$Le=true;$c[]=($Fg?"  ":"ADD ").implode($m[1]);if($m[0]!="")$qe[$m[0]]=$m[1][0];}}if($Fg){if($O!=""){queries("BEGIN");foreach(foreign_keys($O)as$o){$g=array();foreach($o["source"]as$f){if(!$qe[$f])continue
2;$g[]=$qe[$f];}$pc[]="  FOREIGN KEY (".implode(", ",$g).") REFERENCES ".table($o["table"])." (".implode(", ",array_map('idf_escape',$o["target"])).") ON DELETE $o[on_delete] ON UPDATE $o[on_update]";}$u=array();foreach(indexes($O)as$cd=>$t){$g=array();foreach($t["columns"]as$f){if(!$qe[$f])continue
2;$g[]=$qe[$f];}$g="(".implode(", ",$g).")";if($t["type"]!="PRIMARY")$u[]=array($t["type"],$cd,$g);elseif(!$Le)$pc[]="  PRIMARY KEY $g";}}$c=array_merge($c,$pc);if(!queries("CREATE TABLE ".table($O!=""?"adminer_$A":$A)." (\n".implode(",\n",$c)."\n)"))return
false;if($O!=""){if($qe&&!queries("INSERT INTO ".table("adminer_$A")." (".implode(", ",$qe).") SELECT ".implode(", ",array_map('idf_escape',array_keys($qe)))." FROM ".table($O)))return
false;$ug=array();foreach(triggers($O)as$sg=>$eg){$qg=trigger($sg);$ug[]="CREATE TRIGGER ".idf_escape($sg)." ".implode(" ",$eg)." ON ".table($A)."\n$qg[Statement]";}if(!queries("DROP TABLE ".table($O)))return
false;queries("ALTER TABLE ".table("adminer_$A")." RENAME TO ".table($A));if(!alter_indexes($A,$u))return
false;foreach($ug
as$qg){if(!queries($qg))return
false;}queries("COMMIT");}}else{foreach($c
as$W){if(!queries("ALTER TABLE ".table($O)." $W"))return
false;}if($O!=$A&&!queries("ALTER TABLE ".table($O)." RENAME TO ".table($A)))return
false;}if($za)queries("UPDATE sqlite_sequence SET seq = $za WHERE name = ".q($A));return
true;}function
index_sql($O,$S,$A,$g){return"CREATE $S ".($S!="INDEX"?"INDEX ":"").idf_escape($A!=""?$A:uniqid($O."_"))." ON ".table($O)." $g";}function
alter_indexes($O,$c){foreach($c
as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($O,$W[0],$W[1],$W[2])))return
false;}return
true;}function
truncate_tables($Q){return
apply_queries("DELETE FROM",$Q);}function
drop_views($Y){return
apply_queries("DROP VIEW",$Y);}function
drop_tables($Q){return
apply_queries("DROP TABLE",$Q);}function
move_tables($Q,$Y,$Xf){return
false;}function
trigger($A){global$h;if($A=="")return
array("Statement"=>"BEGIN\n\t;\nEND");preg_match('~^CREATE\\s+TRIGGER\\s*(?:[^`"\\s]+|`[^`]*`|"[^"]*")+\\s*([a-z]+)\\s+([a-z]+)\\s+ON\\s*(?:[^`"\\s]+|`[^`]*`|"[^"]*")+\\s*(?:FOR\\s*EACH\\s*ROW\\s)?(.*)~is',$h->result("SELECT sql FROM sqlite_master WHERE name = ".q($A)),$_);return
array("Timing"=>strtoupper($_[1]),"Event"=>strtoupper($_[2]),"Trigger"=>$A,"Statement"=>$_[3]);}function
triggers($O){$H=array();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($O))as$I){preg_match('~^CREATE\\s+TRIGGER\\s*(?:[^`"\\s]+|`[^`]*`|"[^"]*")+\\s*([a-z]+)\\s*([a-z]+)~i',$I["sql"],$_);$H[$I["name"]]=array($_[1],$_[2]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Type"=>array("FOR EACH ROW"),);}function
routine($A,$S){}function
routines(){}function
routine_languages(){}function
begin(){return
queries("BEGIN");}function
insert_into($O,$M){return
queries("INSERT INTO ".table($O).($M?" (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")":"DEFAULT VALUES"));}function
insert_update($O,$M,$Ke){return
queries("REPLACE INTO ".table($O)." (".implode(", ",array_keys($M)).") VALUES (".implode(", ",$M).")");}function
last_id(){global$h;return$h->result("SELECT LAST_INSERT_ROWID()");}function
explain($h,$F){return$h->query("EXPLAIN $F");}function
found_rows($P,$Z){}function
types(){return
array();}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($sf){return
true;}function
create_sql($O,$za){global$h;$H=$h->result("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($O));foreach(indexes($O)as$A=>$t){if($A=='')continue;$H.=";\n\n".index_sql($O,$t['type'],$A,"(".implode(", ",array_map('idf_escape',$t['columns'])).")");}return$H;}function
truncate_sql($O){return"DELETE FROM ".table($O);}function
use_sql($ob){}function
trigger_sql($O,$Jf){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($O)));}function
show_variables(){global$h;$H=array();foreach(array("auto_vacuum","cache_size","count_changes","default_cache_size","empty_result_callbacks","encoding","foreign_keys","full_column_names","fullfsync","journal_mode","journal_size_limit","legacy_file_format","locking_mode","page_size","max_page_count","read_uncommitted","recursive_triggers","reverse_unordered_selects","secure_delete","short_column_names","synchronous","temp_store","temp_store_directory","schema_version","integrity_check","quick_check")as$w)$H[$w]=$h->result("PRAGMA $w");return$H;}function
show_status(){$H=array();foreach(get_vals("PRAGMA compile_options")as$fe){list($w,$W)=explode("=",$fe,2);$H[$w]=$W;}return$H;}function
convert_field($m){}function
unconvert_field($m,$H){return$H;}function
support($ic){return
ereg('^(view|trigger|variables|status|dump|move_col|drop_col)$',$ic);}$v="sqlite";$T=array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0);$If=array_keys($T);$Cg=array();$ee=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");$zc=array("hex","length","lower","round","unixepoch","upper");$Dc=array("avg","count","count distinct","group_concat","max","min","sum");$Hb=array(array(),array("integer|real|numeric"=>"+/-","text"=>"||",));}$Ab["pgsql"]="PostgreSQL";if(isset($_GET["pgsql"])){$He=array("PgSQL","PDO_PgSQL");define("DRIVER","pgsql");if(extension_loaded("pgsql")){class
Min_DB{var$extension="PgSQL",$_link,$_result,$_string,$_database=true,$server_info,$affected_rows,$error;function
_error($Sb,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=ereg_replace('^[^:]*: ','',$l);$this->error=$l;}function
connect($L,$U,$D){global$b;$k=$b->database();set_error_handler(array($this,'_error'));$this->_string="host='".str_replace(":","' port='",addcslashes($L,"'\\"))."' user='".addcslashes($U,"'\\")."' password='".addcslashes($D,"'\\")."'";$this->_link=@pg_connect("$this->_string dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->_link&&$k!=""){$this->_database=false;$this->_link=@pg_connect("$this->_string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->_link){$Mg=pg_version($this->_link);$this->server_info=$Mg["server"];pg_set_client_encoding($this->_link,"UTF8");}return(bool)$this->_link;}function
quote($N){return"'".pg_escape_string($this->_link,$N)."'";}function
select_db($ob){global$b;if($ob==$b->database())return$this->_database;$H=@pg_connect("$this->_string dbname='".addcslashes($ob,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($H)$this->_link=$H;return$H;}function
close(){$this->_link=@pg_connect("$this->_string dbname='postgres'");}function
query($F,$xg=false){$G=@pg_query($this->_link,$F);$this->error="";if(!$G){$this->error=pg_last_error($this->_link);return
false;}elseif(!pg_num_fields($G)){$this->affected_rows=pg_affected_rows($G);return
true;}return
new
Min_Result($G);}function
multi_query($F){return$this->_result=$this->query($F);}function
store_result(){return$this->_result;}function
next_result(){return
false;}function
result($F,$m=0){$G=$this->query($F);if(!$G||!$G->num_rows)return
false;return
pg_fetch_result($G->_result,0,$m);}}class
Min_Result{var$_result,$_offset=0,$num_rows;function
Min_Result($G){$this->_result=$G;$this->num_rows=pg_num_rows($G);}function
fetch_assoc(){return
pg_fetch_assoc($this->_result);}function
fetch_row(){return
pg_fetch_row($this->_result);}function
fetch_field(){$f=$this->_offset++;$H=new
stdClass;if(function_exists('pg_field_table'))$H->orgtable=pg_field_table($this->_result,$f);$H->name=pg_field_name($this->_result,$f);$H->orgname=$H->name;$H->type=pg_field_type($this->_result,$f);$H->charsetnr=($H->type=="bytea"?63:0);return$H;}function
__destruct(){pg_free_result($this->_result);}}}elseif(extension_loaded("pdo_pgsql")){class
Min_DB
extends
Min_PDO{var$extension="PDO_PgSQL";function
connect($L,$U,$D){global$b;$k=$b->database();$N="pgsql:host='".str_replace(":","' port='",addcslashes($L,"'\\"))."' options='-c client_encoding=utf8'";$this->dsn("$N dbname='".($k!=""?addcslashes($k,"'\\"):"postgres")."'",$U,$D);return
true;}function
select_db($ob){global$b;return($b->database()==$ob);}function
close(){}}}function
idf_escape($s){return'"'.str_replace('"','""',$s).'"';}function
table($s){return
idf_escape($s);}function
connect(){global$b;$h=new
Min_DB;$kb=$b->credentials();if($h->connect($kb[0],$kb[1],$kb[2])){if($h->server_info>=9)$h->query("SET application_name = 'Adminer'");return$h;}return$h->error;}function
get_databases(){return
get_vals("SELECT datname FROM pg_database ORDER BY datname");}function
limit($F,$Z,$x,$B=0,$wf=" "){return" $F$Z".($x!==null?$wf."LIMIT $x".($B?" OFFSET $B":""):"");}function
limit1($F,$Z){return" $F$Z";}function
db_collation($k,$Ua){global$h;return$h->result("SHOW LC_COLLATE");}function
engines(){return
array();}function
logged_user(){global$h;return$h->result("SELECT user");}function
tables_list(){return
get_key_vals("SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema() ORDER BY table_name");}function
count_tables($j){return
array();}function
table_status($A=""){$H=array();foreach(get_rows("SELECT relname AS \"Name\", CASE relkind WHEN 'r' THEN 'table' ELSE 'view' END AS \"Engine\", pg_relation_size(oid) AS \"Data_length\", pg_total_relation_size(oid) - pg_relation_size(oid) AS \"Index_length\", obj_description(oid, 'pg_class') AS \"Comment\", relhasoids AS \"Oid\", reltuples as \"Rows\"
FROM pg_class
WHERE relkind IN ('r','v')
AND relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())".($A!=""?" AND relname = ".q($A):""))as$I)$H[$I["Name"]]=$I;return($A!=""?$H[$A]:$H);}function
is_view($P){return$P["Engine"]=="view";}function
fk_support($P){return
true;}function
fields($O){$H=array();foreach(get_rows("SELECT a.attname AS field, format_type(a.atttypid, a.atttypmod) AS full_type, d.adsrc AS default, a.attnotnull::int, col_description(c.oid, a.attnum) AS comment
FROM pg_class c
JOIN pg_namespace n ON c.relnamespace = n.oid
JOIN pg_attribute a ON c.oid = a.attrelid
LEFT JOIN pg_attrdef d ON c.oid = d.adrelid AND a.attnum = d.adnum
WHERE c.relname = ".q($O)."
AND n.nspname = current_schema()
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$I){ereg('(.*)(\\((.*)\\))?',$I["full_type"],$_);list(,$I["type"],,$I["length"])=$_;$I["full_type"]=$I["type"].($I["length"]?"($I[length])":"");$I["null"]=!$I["attnotnull"];$I["auto_increment"]=eregi("^nextval\\(",$I["default"]);$I["privileges"]=array("insert"=>1,"select"=>1,"update"=>1);if(preg_match('~^(.*)::.+$~',$I["default"],$_))$I["default"]=($_[1][0]=="'"?idf_unescape($_[1]):$_[1]);$H[$I["field"]]=$I;}return$H;}function
indexes($O,$i=null){global$h;if(!is_object($i))$i=$h;$H=array();$Rf=$i->result("SELECT oid FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema()) AND relname = ".q($O));$g=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $Rf AND attnum > 0",$i);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey FROM pg_index i, pg_class ci WHERE i.indrelid = $Rf AND ci.oid = i.indexrelid",$i)as$I){$H[$I["relname"]]["type"]=($I["indisprimary"]?"PRIMARY":($I["indisunique"]?"UNIQUE":"INDEX"));$H[$I["relname"]]["columns"]=array();foreach(explode(" ",$I["indkey"])as$Oc)$H[$I["relname"]]["columns"][]=$g[$Oc];$H[$I["relname"]]["lengths"]=array();}return$H;}function
foreign_keys($O){global$ae;$H=array();foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = (SELECT pc.oid FROM pg_class AS pc INNER JOIN pg_namespace AS pn ON (pn.oid = pc.relnamespace) WHERE pc.relname = ".q($O)." AND pn.nspname = current_schema())
AND contype = 'f'::char
ORDER BY conkey, conname")as$I){if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$I['definition'],$_)){$I['source']=array_map('trim',explode(',',$_[1]));$I['table']=$_[2];if(preg_match('~(.+)\.(.+)~',$_[2],$ud)){$I['ns']=$ud[1];$I['table']=$ud[2];}$I['target']=array_map('trim',explode(',',$_[3]));$I['on_delete']=(preg_match("~ON DELETE ($ae)~",$_[4],$ud)?$ud[1]:'NO ACTION');$I['on_update']=(preg_match("~ON UPDATE ($ae)~",$_[4],$ud)?$ud[1]:'NO ACTION');$H[$I['conname']]=$I;}}return$H;}function
view($A){global$h;return
array("select"=>$h->result("SELECT pg_get_viewdef(".q($A).")"));}function
collations(){return
array();}function
information_schema($k){return($k=="information_schema");}function
error(){global$h;$H=h($h->error);if(preg_match('~^(.*\\n)?([^\\n]*)\\n( *)\\^(\\n.*)?$~s',$H,$_))$H=$_[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($_[3]).'})(.*)~','\\1<b>\\2</b>',$_[2]).$_[4];return
nl_br($H);}function
exact_value($W){return
q($W);}function
create_database($k,$e){return
queries("CREATE DATABASE ".idf_escape($k).($e?" ENCODING ".idf_escape($e):""));}function
drop_databases($j){global$h;$h->close();return
apply_queries("DROP DATABASE",$j,'idf_escape');}function
rename_database($A,$e){return
queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($A));}function
auto_increment(){return"";}function
alter_table($O,$A,$n,$pc,$Ya,$Pb,$e,$za,$ze){$c=array();$Te=array();foreach($n
as$m){$f=idf_escape($m[0]);$W=$m[1];if(!$W)$c[]="DROP $f";else{$Jg=$W[5];unset($W[5]);if(isset($W[6])&&$m[0]=="")$W[1]=($W[1]=="bigint"?" big":" ")."serial";if($m[0]=="")$c[]=($O!=""?"ADD ":"  ").implode($W);else{if($f!=$W[0])$Te[]="ALTER TABLE ".table($O)." RENAME $f TO $W[0]";$c[]="ALTER $f TYPE$W[1]";if(!$W[6]){$c[]="ALTER $f ".($W[3]?"SET$W[3]":"DROP DEFAULT");$c[]="ALTER $f ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}}if($m[0]!=""||$Jg!="")$Te[]="COMMENT ON COLUMN ".table($O).".$W[0] IS ".($Jg!=""?substr($Jg,9):"''");}}$c=array_merge($c,$pc);if($O=="")array_unshift($Te,"CREATE TABLE ".table($A)." (\n".implode(",\n",$c)."\n)");elseif($c)array_unshift($Te,"ALTER TABLE ".table($O)."\n".implode(",\n",$c));if($O!=""&&$O!=$A)$Te[]="ALTER TABLE ".table($O)." RENAME TO ".table($A);if($O!=""||$Ya!="")$Te[]="COMMENT ON TABLE ".table($A)." IS ".q($Ya);if($za!=""){}foreach($Te
as$F){if(!queries($F))return
false;}return
true;}function
alter_indexes($O,$c){$hb=array();$Bb=array();foreach($c
as$W){if($W[0]!="INDEX")$hb[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").$W[2]);elseif($W[2]=="DROP")$Bb[]=idf_escape($W[1]);elseif(!queries("CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($O."_"))." ON ".table($O)." $W[2]"))return
false;}return((!$hb||queries("ALTER TABLE ".table($O).implode(",",$hb)))&&(!$Bb||queries("DROP INDEX ".implode(", ",$Bb))));}function
truncate_tables($Q){return
queries("TRUNCATE ".implode(", ",array_map('table',$Q)));return
true;}function
drop_views($Y){return
queries("DROP VIEW ".implode(", ",array_map('table',$Y)));}function
drop_tables($Q){return
queries("DROP TABLE ".implode(", ",array_map('table',$Q)));}function
move_tables($Q,$Y,$Xf){foreach($Q
as$O){if(!queries("ALTER TABLE ".table($O)." SET SCHEMA ".idf_escape($Xf)))return
false;}foreach($Y
as$O){if(!queries("ALTER VIEW ".table($O)." SET SCHEMA ".idf_escape($Xf)))return
false;}return
true;}function
trigger($A){if($A=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$J=get_rows('SELECT trigger_name AS "Trigger", condition_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement" FROM information_schema.triggers WHERE event_object_table = '.q($_GET["trigger"]).' AND trigger_name = '.q($A));return
reset($J);}function
triggers($O){$H=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE event_object_table = ".q($O))as$I)$H[$I["trigger_name"]]=array($I["condition_timing"],$I["event_manipulation"]);return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routines(){return
get_rows('SELECT p.proname AS "ROUTINE_NAME", p.proargtypes AS "ROUTINE_TYPE", pg_catalog.format_type(p.prorettype, NULL) AS "DTD_IDENTIFIER"
FROM pg_catalog.pg_namespace n
JOIN pg_catalog.pg_proc p ON p.pronamespace = n.oid
WHERE n.nspname = current_schema()
ORDER BY p.proname');}function
routine_languages(){return
get_vals("SELECT langname FROM pg_catalog.pg_language");}function
begin(){return
queries("BEGIN");}function
insert_into($O,$M){return
queries("INSERT INTO ".table($O).($M?" (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")":"DEFAULT VALUES"));}function
insert_update($O,$M,$Ke){global$h;$Dg=array();$Z=array();foreach($M
as$w=>$W){$Dg[]="$w = $W";if(isset($Ke[idf_unescape($w)]))$Z[]="$w = $W";}return($Z&&queries("UPDATE ".table($O)." SET ".implode(", ",$Dg)." WHERE ".implode(" AND ",$Z))&&$h->affected_rows)||queries("INSERT INTO ".table($O)." (".implode(", ",array_keys($M)).") VALUES (".implode(", ",$M).")");}function
last_id(){return
0;}function
explain($h,$F){return$h->query("EXPLAIN $F");}function
found_rows($P,$Z){global$h;if(ereg(" rows=([0-9]+)",$h->result("EXPLAIN SELECT * FROM ".idf_escape($P["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$df))return$df[1];return
false;}function
types(){return
get_vals("SELECT typname
FROM pg_type
WHERE typnamespace = (SELECT oid FROM pg_namespace WHERE nspname = current_schema())
AND typtype IN ('b','d','e')
AND typelem = 0");}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){global$h;return$h->result("SELECT current_schema()");}function
set_schema($rf){global$h,$T,$If;$H=$h->query("SET search_path TO ".idf_escape($rf));foreach(types()as$S){if(!isset($T[$S])){$T[$S]=0;$If['User types'][]=$S;}}return$H;}function
use_sql($ob){return"\connect ".idf_escape($ob);}function
show_variables(){return
get_key_vals("SHOW ALL");}function
process_list(){global$h;return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".($h->server_info<9.2?"procpid":"pid"));}function
show_status(){}function
convert_field($m){}function
unconvert_field($m,$H){return$H;}function
support($ic){return
ereg('^(comment|view|scheme|processlist|sequence|trigger|type|variables|drop_col)$',$ic);}$v="pgsql";$T=array();$If=array();foreach(array('Numbers'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'Date and time'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'Strings'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'Binary'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'Network'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"txid_snapshot"=>0),'Geometry'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),)as$w=>$W){$T+=$W;$If[$w]=array_keys($W);}$Cg=array();$ee=array("=","<",">","<=",">=","!=","~","!~","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");$zc=array("char_length","lower","round","to_hex","to_timestamp","upper");$Dc=array("avg","count","count distinct","max","min","sum");$Hb=array(array("char"=>"md5","date|time"=>"now",),array("int|numeric|real|money"=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",));}$Ab["oracle"]="Oracle";if(isset($_GET["oracle"])){$He=array("OCI8","PDO_OCI");define("DRIVER","oracle");if(extension_loaded("oci8")){class
Min_DB{var$extension="oci8",$_link,$_result,$server_info,$affected_rows,$errno,$error;function
_error($Sb,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=ereg_replace('^[^:]*: ','',$l);$this->error=$l;}function
connect($L,$U,$D){$this->_link=@oci_new_connect($U,$D,$L,"AL32UTF8");if($this->_link){$this->server_info=oci_server_version($this->_link);return
true;}$l=oci_error();$this->error=$l["message"];return
false;}function
quote($N){return"'".str_replace("'","''",$N)."'";}function
select_db($ob){return
true;}function
query($F,$xg=false){$G=oci_parse($this->_link,$F);$this->error="";if(!$G){$l=oci_error($this->_link);$this->errno=$l["code"];$this->error=$l["message"];return
false;}set_error_handler(array($this,'_error'));$H=@oci_execute($G);restore_error_handler();if($H){if(oci_num_fields($G))return
new
Min_Result($G);$this->affected_rows=oci_num_rows($G);}return$H;}function
multi_query($F){return$this->_result=$this->query($F);}function
store_result(){return$this->_result;}function
next_result(){return
false;}function
result($F,$m=1){$G=$this->query($F);if(!is_object($G)||!oci_fetch($G->_result))return
false;return
oci_result($G->_result,$m);}}class
Min_Result{var$_result,$_offset=1,$num_rows;function
Min_Result($G){$this->_result=$G;}function
_convert($I){foreach((array)$I
as$w=>$W){if(is_a($W,'OCI-Lob'))$I[$w]=$W->load();}return$I;}function
fetch_assoc(){return$this->_convert(oci_fetch_assoc($this->_result));}function
fetch_row(){return$this->_convert(oci_fetch_row($this->_result));}function
fetch_field(){$f=$this->_offset++;$H=new
stdClass;$H->name=oci_field_name($this->_result,$f);$H->orgname=$H->name;$H->type=oci_field_type($this->_result,$f);$H->charsetnr=(ereg("raw|blob|bfile",$H->type)?63:0);return$H;}function
__destruct(){oci_free_statement($this->_result);}}}elseif(extension_loaded("pdo_oci")){class
Min_DB
extends
Min_PDO{var$extension="PDO_OCI";function
connect($L,$U,$D){$this->dsn("oci:dbname=//$L;charset=AL32UTF8",$U,$D);return
true;}function
select_db($ob){return
true;}}}function
idf_escape($s){return'"'.str_replace('"','""',$s).'"';}function
table($s){return
idf_escape($s);}function
connect(){global$b;$h=new
Min_DB;$kb=$b->credentials();if($h->connect($kb[0],$kb[1],$kb[2]))return$h;return$h->error;}function
get_databases(){return
get_vals("SELECT tablespace_name FROM user_tablespaces");}function
limit($F,$Z,$x,$B=0,$wf=" "){return($B?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $F$Z) t WHERE rownum <= ".($x+$B).") WHERE rnum > $B":($x!==null?" * FROM (SELECT $F$Z) WHERE rownum <= ".($x+$B):" $F$Z"));}function
limit1($F,$Z){return" $F$Z";}function
db_collation($k,$Ua){global$h;return$h->result("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
engines(){return
array();}function
logged_user(){global$h;return$h->result("SELECT USER FROM DUAL");}function
tables_list(){return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."
UNION SELECT view_name, 'view' FROM user_views");}function
count_tables($j){return
array();}function
table_status($A=""){$H=array();$tf=q($A);foreach(get_rows('SELECT table_name "Name", \'table\' "Engine", avg_row_len * num_rows "Data_length", num_rows "Rows" FROM all_tables WHERE tablespace_name = '.q(DB).($A!=""?" AND table_name = $tf":"")."
UNION SELECT view_name, 'view', 0, 0 FROM user_views".($A!=""?" WHERE view_name = $tf":""))as$I){if($A!="")return$I;$H[$I["Name"]]=$I;}return$H;}function
is_view($P){return$P["Engine"]=="view";}function
fk_support($P){return
true;}function
fields($O){$H=array();foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($O)." ORDER BY column_id")as$I){$S=$I["DATA_TYPE"];$od="$I[DATA_PRECISION],$I[DATA_SCALE]";if($od==",")$od=$I["DATA_LENGTH"];$H[$I["COLUMN_NAME"]]=array("field"=>$I["COLUMN_NAME"],"full_type"=>$S.($od?"($od)":""),"type"=>strtolower($S),"length"=>$od,"default"=>$I["DATA_DEFAULT"],"null"=>($I["NULLABLE"]=="Y"),"privileges"=>array("insert"=>1,"select"=>1,"update"=>1),);}return$H;}function
indexes($O,$i=null){$H=array();foreach(get_rows("SELECT uic.*, uc.constraint_type
FROM user_ind_columns uic
LEFT JOIN user_constraints uc ON uic.index_name = uc.constraint_name AND uic.table_name = uc.table_name
WHERE uic.table_name = ".q($O)."
ORDER BY uc.constraint_type, uic.column_position",$i)as$I){$H[$I["INDEX_NAME"]]["type"]=($I["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($I["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$H[$I["INDEX_NAME"]]["columns"][]=$I["COLUMN_NAME"];$H[$I["INDEX_NAME"]]["lengths"][]=($I["CHAR_LENGTH"]&&$I["CHAR_LENGTH"]!=$I["COLUMN_LENGTH"]?$I["CHAR_LENGTH"]:null);}return$H;}function
view($A){$J=get_rows('SELECT text "select" FROM user_views WHERE view_name = '.q($A));return
reset($J);}function
collations(){return
array();}function
information_schema($k){return
false;}function
error(){global$h;return
h($h->error);}function
exact_value($W){return
q($W);}function
explain($h,$F){$h->query("EXPLAIN PLAN FOR $F");return$h->query("SELECT * FROM plan_table");}function
found_rows($P,$Z){}function
alter_table($O,$A,$n,$pc,$Ya,$Pb,$e,$za,$ze){$c=$Bb=array();foreach($n
as$m){$W=$m[1];if($W&&$m[0]!=""&&idf_escape($m[0])!=$W[0])queries("ALTER TABLE ".table($O)." RENAME COLUMN ".idf_escape($m[0])." TO $W[0]");if($W)$c[]=($O!=""?($m[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($O!=""?")":"");else$Bb[]=idf_escape($m[0]);}if($O=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$c)."\n)");return(!$c||queries("ALTER TABLE ".table($O)."\n".implode("\n",$c)))&&(!$Bb||queries("ALTER TABLE ".table($O)." DROP (".implode(", ",$Bb).")"))&&($O==$A||queries("ALTER TABLE ".table($O)." RENAME TO ".table($A)));}function
foreign_keys($O){return
array();}function
truncate_tables($Q){return
apply_queries("TRUNCATE TABLE",$Q);}function
drop_views($Y){return
apply_queries("DROP VIEW",$Y);}function
drop_tables($Q){return
apply_queries("DROP TABLE",$Q);}function
begin(){return
true;}function
insert_into($O,$M){return
queries("INSERT INTO ".table($O)." (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")");}function
last_id(){return
0;}function
schemas(){return
get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX'))");}function
get_schema(){global$h;return$h->result("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($sf){global$h;return$h->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($sf));}function
show_variables(){return
get_key_vals('SELECT name, display_value FROM v$parameter');}function
process_list(){return
get_rows('SELECT sess.process AS "process", sess.username AS "user", sess.schemaname AS "schema", sess.status AS "status", sess.wait_class AS "wait_class", sess.seconds_in_wait AS "seconds_in_wait", sql.sql_text AS "sql_text", sess.machine AS "machine", sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
show_status(){$J=get_rows('SELECT * FROM v$instance');return
reset($J);}function
convert_field($m){}function
unconvert_field($m,$H){return$H;}function
support($ic){return
ereg("view|scheme|processlist|drop_col|variables|status",$ic);}$v="oracle";$T=array();$If=array();foreach(array('Numbers'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'Date and time'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'Strings'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'Binary'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),)as$w=>$W){$T+=$W;$If[$w]=array_keys($W);}$Cg=array();$ee=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");$zc=array("length","lower","round","upper");$Dc=array("avg","count","count distinct","max","min","sum");$Hb=array(array("date"=>"current_date","timestamp"=>"current_timestamp",),array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",));}$Ab["mssql"]="MS SQL";if(isset($_GET["mssql"])){$He=array("SQLSRV","MSSQL");define("DRIVER","mssql");if(extension_loaded("sqlsrv")){class
Min_DB{var$extension="sqlsrv",$_link,$_result,$server_info,$affected_rows,$errno,$error;function
_get_error(){$this->error="";foreach(sqlsrv_errors()as$l){$this->errno=$l["code"];$this->error.="$l[message]\n";}$this->error=rtrim($this->error);}function
connect($L,$U,$D){$this->_link=@sqlsrv_connect($L,array("UID"=>$U,"PWD"=>$D,"CharacterSet"=>"UTF-8"));if($this->_link){$Pc=sqlsrv_server_info($this->_link);$this->server_info=$Pc['SQLServerVersion'];}else$this->_get_error();return(bool)$this->_link;}function
quote($N){return"'".str_replace("'","''",$N)."'";}function
select_db($ob){return$this->query("USE ".idf_escape($ob));}function
query($F,$xg=false){$G=sqlsrv_query($this->_link,$F);$this->error="";if(!$G){$this->_get_error();return
false;}return$this->store_result($G);}function
multi_query($F){$this->_result=sqlsrv_query($this->_link,$F);$this->error="";if(!$this->_result){$this->_get_error();return
false;}return
true;}function
store_result($G=null){if(!$G)$G=$this->_result;if(sqlsrv_field_metadata($G))return
new
Min_Result($G);$this->affected_rows=sqlsrv_rows_affected($G);return
true;}function
next_result(){return
sqlsrv_next_result($this->_result);}function
result($F,$m=0){$G=$this->query($F);if(!is_object($G))return
false;$I=$G->fetch_row();return$I[$m];}}class
Min_Result{var$_result,$_offset=0,$_fields,$num_rows;function
Min_Result($G){$this->_result=$G;}function
_convert($I){foreach((array)$I
as$w=>$W){if(is_a($W,'DateTime'))$I[$w]=$W->format("Y-m-d H:i:s");}return$I;}function
fetch_assoc(){return$this->_convert(sqlsrv_fetch_array($this->_result,SQLSRV_FETCH_ASSOC,SQLSRV_SCROLL_NEXT));}function
fetch_row(){return$this->_convert(sqlsrv_fetch_array($this->_result,SQLSRV_FETCH_NUMERIC,SQLSRV_SCROLL_NEXT));}function
fetch_field(){if(!$this->_fields)$this->_fields=sqlsrv_field_metadata($this->_result);$m=$this->_fields[$this->_offset++];$H=new
stdClass;$H->name=$m["Name"];$H->orgname=$m["Name"];$H->type=($m["Type"]==1?254:0);return$H;}function
seek($B){for($q=0;$q<$B;$q++)sqlsrv_fetch($this->_result);}function
__destruct(){sqlsrv_free_stmt($this->_result);}}}elseif(extension_loaded("mssql")){class
Min_DB{var$extension="MSSQL",$_link,$_result,$server_info,$affected_rows,$error;function
connect($L,$U,$D){$this->_link=@mssql_connect($L,$U,$D);if($this->_link){$G=$this->query("SELECT SERVERPROPERTY('ProductLevel'), SERVERPROPERTY('Edition')");$I=$G->fetch_row();$this->server_info=$this->result("sp_server_info 2",2)." [$I[0]] $I[1]";}else$this->error=mssql_get_last_message();return(bool)$this->_link;}function
quote($N){return"'".str_replace("'","''",$N)."'";}function
select_db($ob){return
mssql_select_db($ob);}function
query($F,$xg=false){$G=mssql_query($F,$this->_link);$this->error="";if(!$G){$this->error=mssql_get_last_message();return
false;}if($G===true){$this->affected_rows=mssql_rows_affected($this->_link);return
true;}return
new
Min_Result($G);}function
multi_query($F){return$this->_result=$this->query($F);}function
store_result(){return$this->_result;}function
next_result(){return
mssql_next_result($this->_result);}function
result($F,$m=0){$G=$this->query($F);if(!is_object($G))return
false;return
mssql_result($G->_result,0,$m);}}class
Min_Result{var$_result,$_offset=0,$_fields,$num_rows;function
Min_Result($G){$this->_result=$G;$this->num_rows=mssql_num_rows($G);}function
fetch_assoc(){return
mssql_fetch_assoc($this->_result);}function
fetch_row(){return
mssql_fetch_row($this->_result);}function
num_rows(){return
mssql_num_rows($this->_result);}function
fetch_field(){$H=mssql_fetch_field($this->_result);$H->orgtable=$H->table;$H->orgname=$H->name;return$H;}function
seek($B){mssql_data_seek($this->_result,$B);}function
__destruct(){mssql_free_result($this->_result);}}}function
idf_escape($s){return"[".str_replace("]","]]",$s)."]";}function
table($s){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($s);}function
connect(){global$b;$h=new
Min_DB;$kb=$b->credentials();if($h->connect($kb[0],$kb[1],$kb[2]))return$h;return$h->error;}function
get_databases(){return
get_vals("EXEC sp_databases");}function
limit($F,$Z,$x,$B=0,$wf=" "){return($x!==null?" TOP (".($x+$B).")":"")." $F$Z";}function
limit1($F,$Z){return
limit($F,$Z,1);}function
db_collation($k,$Ua){global$h;return$h->result("SELECT collation_name FROM sys.databases WHERE name =  ".q($k));}function
engines(){return
array();}function
logged_user(){global$h;return$h->result("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables($j){global$h;$H=array();foreach($j
as$k){$h->select_db($k);$H[$k]=$h->result("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$H;}function
table_status($A=""){$H=array();foreach(get_rows("SELECT name AS Name, type_desc AS Engine FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V')".($A!=""?" AND name = ".q($A):""))as$I){if($A!="")return$I;$H[$I["Name"]]=$I;}return$H;}function
is_view($P){return$P["Engine"]=="VIEW";}function
fk_support($P){return
true;}function
fields($O){$H=array();foreach(get_rows("SELECT c.*, t.name type, d.definition [default]
FROM sys.all_columns c
JOIN sys.all_objects o ON c.object_id = o.object_id
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.parent_column_id
WHERE o.schema_id = SCHEMA_ID(".q(get_schema()).") AND o.type IN ('S', 'U', 'V') AND o.name = ".q($O))as$I){$S=$I["type"];$od=(ereg("char|binary",$S)?$I["max_length"]:($S=="decimal"?"$I[precision],$I[scale]":""));$H[$I["name"]]=array("field"=>$I["name"],"full_type"=>$S.($od?"($od)":""),"type"=>$S,"length"=>$od,"default"=>$I["default"],"null"=>$I["is_nullable"],"auto_increment"=>$I["is_identity"],"collation"=>$I["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1),"primary"=>$I["is_identity"],);}return$H;}function
indexes($O,$i=null){$H=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($O),$i)as$I){$H[$I["name"]]["type"]=($I["is_primary_key"]?"PRIMARY":($I["is_unique"]?"UNIQUE":"INDEX"));$H[$I["name"]]["lengths"]=array();$H[$I["name"]]["columns"][$I["key_ordinal"]]=$I["column_name"];}return$H;}function
view($A){global$h;return
array("select"=>preg_replace('~^(?:[^[]|\\[[^]]*])*\\s+AS\\s+~isU','',$h->result("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($A))));}function
collations(){$H=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$e)$H[ereg_replace("_.*","",$e)][]=$e;return$H;}function
information_schema($k){return
false;}function
error(){global$h;return
nl_br(h(preg_replace('~^(\\[[^]]*])+~m','',$h->error)));}function
exact_value($W){return
q($W);}function
create_database($k,$e){return
queries("CREATE DATABASE ".idf_escape($k).(eregi('^[a-z0-9_]+$',$e)?" COLLATE $e":""));}function
drop_databases($j){return
queries("DROP DATABASE ".implode(", ",array_map('idf_escape',$j)));}function
rename_database($A,$e){if(eregi('^[a-z0-9_]+$',$e))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $e");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($A));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".(+$_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($O,$A,$n,$pc,$Ya,$Pb,$e,$za,$ze){$c=array();foreach($n
as$m){$f=idf_escape($m[0]);$W=$m[1];if(!$W)$c["DROP"][]=" COLUMN $f";else{$W[1]=preg_replace("~( COLLATE )'(\\w+)'~","\\1\\2",$W[1]);if($m[0]=="")$c["ADD"][]="\n  ".implode("",$W).($O==""?substr($pc[$W[0]],16+strlen($W[0])):"");else{unset($W[6]);if($f!=$W[0])queries("EXEC sp_rename ".q(table($O).".$f").", ".q(idf_unescape($W[0])).", 'COLUMN'");$c["ALTER COLUMN ".implode("",$W)][]="";}}}if($O=="")return
queries("CREATE TABLE ".table($A)." (".implode(",",(array)$c["ADD"])."\n)");if($O!=$A)queries("EXEC sp_rename ".q(table($O)).", ".q($A));if($pc)$c[""]=$pc;foreach($c
as$w=>$W){if(!queries("ALTER TABLE ".idf_escape($A)." $w".implode(",",$W)))return
false;}return
true;}function
alter_indexes($O,$c){$t=array();$Bb=array();foreach($c
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$Bb[]=idf_escape($W[1]);else$t[]=idf_escape($W[1])." ON ".table($O);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($O."_"))." ON ".table($O):"ALTER TABLE ".table($O)." ADD PRIMARY KEY")." $W[2]"))return
false;}return(!$t||queries("DROP INDEX ".implode(", ",$t)))&&(!$Bb||queries("ALTER TABLE ".table($O)." DROP ".implode(", ",$Bb)));}function
begin(){return
queries("BEGIN TRANSACTION");}function
insert_into($O,$M){return
queries("INSERT INTO ".table($O).($M?" (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")":"DEFAULT VALUES"));}function
insert_update($O,$M,$Ke){$Dg=array();$Z=array();foreach($M
as$w=>$W){$Dg[]="$w = $W";if(isset($Ke[idf_unescape($w)]))$Z[]="$w = $W";}return
queries("MERGE ".table($O)." USING (VALUES(".implode(", ",$M).")) AS source (c".implode(", c",range(1,count($M))).") ON ".implode(" AND ",$Z)." WHEN MATCHED THEN UPDATE SET ".implode(", ",$Dg)." WHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($M)).") VALUES (".implode(", ",$M).");");}function
last_id(){global$h;return$h->result("SELECT SCOPE_IDENTITY()");}function
explain($h,$F){$h->query("SET SHOWPLAN_ALL ON");$H=$h->query($F);$h->query("SET SHOWPLAN_ALL OFF");return$H;}function
found_rows($P,$Z){}function
foreign_keys($O){$H=array();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($O))as$I){$o=&$H[$I["FK_NAME"]];$o["table"]=$I["PKTABLE_NAME"];$o["source"][]=$I["FKCOLUMN_NAME"];$o["target"][]=$I["PKCOLUMN_NAME"];}return$H;}function
truncate_tables($Q){return
apply_queries("TRUNCATE TABLE",$Q);}function
drop_views($Y){return
queries("DROP VIEW ".implode(", ",array_map('table',$Y)));}function
drop_tables($Q){return
queries("DROP TABLE ".implode(", ",array_map('table',$Q)));}function
move_tables($Q,$Y,$Xf){return
apply_queries("ALTER SCHEMA ".idf_escape($Xf)." TRANSFER",array_merge($Q,$Y));}function
trigger($A){if($A=="")return
array();$J=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($A));$H=reset($J);if($H)$H["Statement"]=preg_replace('~^.+\\s+AS\\s+~isU','',$H["text"]);return$H;}function
triggers($O){$H=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE' WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($O))as$I)$H[$I["name"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){global$h;if($_GET["ns"]!="")return$_GET["ns"];return$h->result("SELECT SCHEMA_NAME()");}function
set_schema($rf){return
true;}function
use_sql($ob){return"USE ".idf_escape($ob);}function
show_variables(){return
array();}function
show_status(){return
array();}function
convert_field($m){}function
unconvert_field($m,$H){return$H;}function
support($ic){return
ereg('^(scheme|trigger|view|drop_col)$',$ic);}$v="mssql";$T=array();$If=array();foreach(array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20),'Date and time'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>10),'Strings'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823),'Binary'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),)as$w=>$W){$T+=$W;$If[$w]=array_keys($W);}$Cg=array();$ee=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");$zc=array("len","lower","round","upper");$Dc=array("avg","count","count distinct","max","min","sum");$Hb=array(array("date|time"=>"getdate",),array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",));}$Ab=array("server"=>"MySQL")+$Ab;if(!defined("DRIVER")){$He=array("MySQLi","MySQL","PDO_MySQL");define("DRIVER","server");if(extension_loaded("mysqli")){class
Min_DB
extends
MySQLi{var$extension="MySQLi";function
Min_DB(){parent::init();}function
connect($L,$U,$D){mysqli_report(MYSQLI_REPORT_OFF);list($Ic,$De)=explode(":",$L,2);$H=@$this->real_connect(($L!=""?$Ic:ini_get("mysqli.default_host")),($L.$U!=""?$U:ini_get("mysqli.default_user")),($L.$U.$D!=""?$D:ini_get("mysqli.default_pw")),null,(is_numeric($De)?$De:ini_get("mysqli.default_port")),(!is_numeric($De)?$De:null));if($H){if(method_exists($this,'set_charset'))$this->set_charset("utf8");else$this->query("SET NAMES utf8");}return$H;}function
result($F,$m=0){$G=$this->query($F);if(!$G)return
false;$I=$G->fetch_array();return$I[$m];}function
quote($N){return"'".$this->escape_string($N)."'";}}}elseif(extension_loaded("mysql")&&!(ini_get("sql.safe_mode")&&extension_loaded("pdo_mysql"))){class
Min_DB{var$extension="MySQL",$server_info,$affected_rows,$errno,$error,$_link,$_result;function
connect($L,$U,$D){$this->_link=@mysql_connect(($L!=""?$L:ini_get("mysql.default_host")),("$L$U"!=""?$U:ini_get("mysql.default_user")),("$L$U$D"!=""?$D:ini_get("mysql.default_password")),true,131072);if($this->_link){$this->server_info=mysql_get_server_info($this->_link);if(function_exists('mysql_set_charset'))mysql_set_charset("utf8",$this->_link);else$this->query("SET NAMES utf8");}else$this->error=mysql_error();return(bool)$this->_link;}function
quote($N){return"'".mysql_real_escape_string($N,$this->_link)."'";}function
select_db($ob){return
mysql_select_db($ob,$this->_link);}function
query($F,$xg=false){$G=@($xg?mysql_unbuffered_query($F,$this->_link):mysql_query($F,$this->_link));$this->error="";if(!$G){$this->errno=mysql_errno($this->_link);$this->error=mysql_error($this->_link);return
false;}if($G===true){$this->affected_rows=mysql_affected_rows($this->_link);$this->info=mysql_info($this->_link);return
true;}return
new
Min_Result($G);}function
multi_query($F){return$this->_result=$this->query($F);}function
store_result(){return$this->_result;}function
next_result(){return
false;}function
result($F,$m=0){$G=$this->query($F);if(!$G||!$G->num_rows)return
false;return
mysql_result($G->_result,0,$m);}}class
Min_Result{var$num_rows,$_result,$_offset=0;function
Min_Result($G){$this->_result=$G;$this->num_rows=mysql_num_rows($G);}function
fetch_assoc(){return
mysql_fetch_assoc($this->_result);}function
fetch_row(){return
mysql_fetch_row($this->_result);}function
fetch_field(){$H=mysql_fetch_field($this->_result,$this->_offset++);$H->orgtable=$H->table;$H->orgname=$H->name;$H->charsetnr=($H->blob?63:0);return$H;}function
__destruct(){mysql_free_result($this->_result);}}}elseif(extension_loaded("pdo_mysql")){class
Min_DB
extends
Min_PDO{var$extension="PDO_MySQL";function
connect($L,$U,$D){$this->dsn("mysql:host=".str_replace(":",";unix_socket=",preg_replace('~:(\\d)~',';port=\\1',$L)),$U,$D);$this->query("SET NAMES utf8");return
true;}function
select_db($ob){return$this->query("USE ".idf_escape($ob));}function
query($F,$xg=false){$this->setAttribute(1000,!$xg);return
parent::query($F,$xg);}}}function
idf_escape($s){return"`".str_replace("`","``",$s)."`";}function
table($s){return
idf_escape($s);}function
connect(){global$b;$h=new
Min_DB;$kb=$b->credentials();if($h->connect($kb[0],$kb[1],$kb[2])){$h->query("SET sql_quote_show_create = 1, autocommit = 1");return$h;}$H=$h->error;if(function_exists('iconv')&&!is_utf8($H)&&strlen($pf=iconv("windows-1250","utf-8",$H))>strlen($H))$H=$pf;return$H;}function
get_databases($oc){global$h;$H=get_session("dbs");if($H===null){$F=($h->server_info>=5?"SELECT SCHEMA_NAME FROM information_schema.SCHEMATA":"SHOW DATABASES");$H=($oc?slow_query($F):get_vals($F));restart_session();set_session("dbs",$H);stop_session();}return$H;}function
limit($F,$Z,$x,$B=0,$wf=" "){return" $F$Z".($x!==null?$wf."LIMIT $x".($B?" OFFSET $B":""):"");}function
limit1($F,$Z){return
limit($F,$Z,1);}function
db_collation($k,$Ua){global$h;$H=null;$hb=$h->result("SHOW CREATE DATABASE ".idf_escape($k),1);if(preg_match('~ COLLATE ([^ ]+)~',$hb,$_))$H=$_[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$hb,$_))$H=$Ua[$_[1]][-1];return$H;}function
engines(){$H=array();foreach(get_rows("SHOW ENGINES")as$I){if(ereg("YES|DEFAULT",$I["Support"]))$H[]=$I["Engine"];}return$H;}function
logged_user(){global$h;return$h->result("SELECT USER()");}function
tables_list(){global$h;return
get_key_vals("SHOW".($h->server_info>=5?" FULL":"")." TABLES");}function
count_tables($j){$H=array();foreach($j
as$k)$H[$k]=count(get_vals("SHOW TABLES IN ".idf_escape($k)));return$H;}function
table_status($A="",$hc=false){global$h;$H=array();foreach(get_rows($hc&&$h->server_info>=5?"SELECT TABLE_NAME AS Name, Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()".($A!=""?" AND TABLE_NAME = ".q($A):""):"SHOW TABLE STATUS".($A!=""?" LIKE ".q(addcslashes($A,"%_\\")):""))as$I){if($I["Engine"]=="InnoDB")$I["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\\1',$I["Comment"]);if(!isset($I["Engine"]))$I["Comment"]="";if($A!="")return$I;$H[$I["Name"]]=$I;}return$H;}function
is_view($P){return!isset($P["Engine"]);}function
fk_support($P){return
eregi("InnoDB|IBMDB2I",$P["Engine"]);}function
fields($O){$H=array();foreach(get_rows("SHOW FULL COLUMNS FROM ".table($O))as$I){preg_match('~^([^( ]+)(?:\\((.+)\\))?( unsigned)?( zerofill)?$~',$I["Type"],$_);$H[$I["Field"]]=array("field"=>$I["Field"],"full_type"=>$I["Type"],"type"=>$_[1],"length"=>$_[2],"unsigned"=>ltrim($_[3].$_[4]),"default"=>($I["Default"]!=""||ereg("char|set",$_[1])?$I["Default"]:null),"null"=>($I["Null"]=="YES"),"auto_increment"=>($I["Extra"]=="auto_increment"),"on_update"=>(eregi('^on update (.+)',$I["Extra"],$_)?$_[1]:""),"collation"=>$I["Collation"],"privileges"=>array_flip(explode(",",$I["Privileges"])),"comment"=>$I["Comment"],"primary"=>($I["Key"]=="PRI"),);}return$H;}function
indexes($O,$i=null){$H=array();foreach(get_rows("SHOW INDEX FROM ".table($O),$i)as$I){$H[$I["Key_name"]]["type"]=($I["Key_name"]=="PRIMARY"?"PRIMARY":($I["Index_type"]=="FULLTEXT"?"FULLTEXT":($I["Non_unique"]?"INDEX":"UNIQUE")));$H[$I["Key_name"]]["columns"][]=$I["Column_name"];$H[$I["Key_name"]]["lengths"][]=$I["Sub_part"];}return$H;}function
foreign_keys($O){global$h,$ae;static$Be='`(?:[^`]|``)+`';$H=array();$ib=$h->result("SHOW CREATE TABLE ".table($O),1);if($ib){preg_match_all("~CONSTRAINT ($Be) FOREIGN KEY \\(((?:$Be,? ?)+)\\) REFERENCES ($Be)(?:\\.($Be))? \\(((?:$Be,? ?)+)\\)(?: ON DELETE ($ae))?(?: ON UPDATE ($ae))?~",$ib,$vd,PREG_SET_ORDER);foreach($vd
as$_){preg_match_all("~$Be~",$_[2],$Bf);preg_match_all("~$Be~",$_[5],$Xf);$H[idf_unescape($_[1])]=array("db"=>idf_unescape($_[4]!=""?$_[3]:$_[4]),"table"=>idf_unescape($_[4]!=""?$_[4]:$_[3]),"source"=>array_map('idf_unescape',$Bf[0]),"target"=>array_map('idf_unescape',$Xf[0]),"on_delete"=>($_[6]?$_[6]:"RESTRICT"),"on_update"=>($_[7]?$_[7]:"RESTRICT"),);}}return$H;}function
view($A){global$h;return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\\s+AS\\s+~isU','',$h->result("SHOW CREATE VIEW ".table($A),1)));}function
collations(){$H=array();foreach(get_rows("SHOW COLLATION")as$I){if($I["Default"])$H[$I["Charset"]][-1]=$I["Collation"];else$H[$I["Charset"]][]=$I["Collation"];}ksort($H);foreach($H
as$w=>$W)asort($H[$w]);return$H;}function
information_schema($k){global$h;return($h->server_info>=5&&$k=="information_schema")||($h->server_info>=5.5&&$k=="performance_schema");}function
error(){global$h;return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",$h->error));}function
error_line(){global$h;if(ereg(' at line ([0-9]+)$',$h->error,$df))return$df[1]-1;}function
exact_value($W){return
q($W)." COLLATE utf8_bin";}function
create_database($k,$e){set_session("dbs",null);return
queries("CREATE DATABASE ".idf_escape($k).($e?" COLLATE ".q($e):""));}function
drop_databases($j){restart_session();set_session("dbs",null);return
apply_queries("DROP DATABASE",$j,'idf_escape');}function
rename_database($A,$e){if(create_database($A,$e)){$ef=array();foreach(tables_list()as$O=>$S)$ef[]=table($O)." TO ".idf_escape($A).".".table($O);if(!$ef||queries("RENAME TABLE ".implode(", ",$ef))){queries("DROP DATABASE ".idf_escape(DB));return
true;}}return
false;}function
auto_increment(){$_a=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$t){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$t["columns"],true)){$_a="";break;}if($t["type"]=="PRIMARY")$_a=" UNIQUE";}}return" AUTO_INCREMENT$_a";}function
alter_table($O,$A,$n,$pc,$Ya,$Pb,$e,$za,$ze){$c=array();foreach($n
as$m)$c[]=($m[1]?($O!=""?($m[0]!=""?"CHANGE ".idf_escape($m[0]):"ADD"):" ")." ".implode($m[1]).($O!=""?$m[2]:""):"DROP ".idf_escape($m[0]));$c=array_merge($c,$pc);$Ff="COMMENT=".q($Ya).($Pb?" ENGINE=".q($Pb):"").($e?" COLLATE ".q($e):"").($za!=""?" AUTO_INCREMENT=$za":"").$ze;if($O=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$c)."\n) $Ff");if($O!=$A)$c[]="RENAME TO ".table($A);$c[]=$Ff;return
queries("ALTER TABLE ".table($O)."\n".implode(",\n",$c));}function
alter_indexes($O,$c){foreach($c
as$w=>$W)$c[$w]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"").$W[2]);return
queries("ALTER TABLE ".table($O).implode(",",$c));}function
truncate_tables($Q){return
apply_queries("TRUNCATE TABLE",$Q);}function
drop_views($Y){return
queries("DROP VIEW ".implode(", ",array_map('table',$Y)));}function
drop_tables($Q){return
queries("DROP TABLE ".implode(", ",array_map('table',$Q)));}function
move_tables($Q,$Y,$Xf){$ef=array();foreach(array_merge($Q,$Y)as$O)$ef[]=table($O)." TO ".idf_escape($Xf).".".table($O);return
queries("RENAME TABLE ".implode(", ",$ef));}function
copy_tables($Q,$Y,$Xf){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($Q
as$O){$A=($Xf==DB?table("copy_$O"):idf_escape($Xf).".".table($O));if(!queries("DROP TABLE IF EXISTS $A")||!queries("CREATE TABLE $A LIKE ".table($O))||!queries("INSERT INTO $A SELECT * FROM ".table($O)))return
false;}foreach($Y
as$O){$A=($Xf==DB?table("copy_$O"):idf_escape($Xf).".".table($O));$Ng=view($O);if(!queries("DROP VIEW IF EXISTS $A")||!queries("CREATE VIEW $A AS $Ng[select]"))return
false;}return
true;}function
trigger($A){if($A=="")return
array();$J=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($A));return
reset($J);}function
triggers($O){$H=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($O,"%_\\")))as$I)$H[$I["Trigger"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Type"=>array("FOR EACH ROW"),);}function
routine($A,$S){global$h,$Rb,$Rc,$T;$ua=array("bool","boolean","integer","double precision","real","dec","numeric","fixed","national char","national varchar");$wg="((".implode("|",array_merge(array_keys($T),$ua)).")\\b(?:\\s*\\(((?:[^'\")]*|$Rb)+)\\))?\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?)(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s]+)['\"]?)?";$Be="\\s*(".($S=="FUNCTION"?"":$Rc).")?\\s*(?:`((?:[^`]|``)*)`\\s*|\\b(\\S+)\\s+)$wg";$hb=$h->result("SHOW CREATE $S ".idf_escape($A),2);preg_match("~\\(((?:$Be\\s*,?)*)\\)\\s*".($S=="FUNCTION"?"RETURNS\\s+$wg\\s+":"")."(.*)~is",$hb,$_);$n=array();preg_match_all("~$Be\\s*,?~is",$_[1],$vd,PREG_SET_ORDER);foreach($vd
as$ue){$A=str_replace("``","`",$ue[2]).$ue[3];$n[]=array("field"=>$A,"type"=>strtolower($ue[5]),"length"=>preg_replace_callback("~$Rb~s",'normalize_enum',$ue[6]),"unsigned"=>strtolower(preg_replace('~\\s+~',' ',trim("$ue[8] $ue[7]"))),"null"=>1,"full_type"=>$ue[4],"inout"=>strtoupper($ue[1]),"collation"=>strtolower($ue[9]),);}if($S!="FUNCTION")return
array("fields"=>$n,"definition"=>$_[11]);return
array("fields"=>$n,"returns"=>array("type"=>$_[12],"length"=>$_[13],"unsigned"=>$_[15],"collation"=>$_[16]),"definition"=>$_[17],"language"=>"SQL",);}function
routines(){return
get_rows("SELECT ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = ".q(DB));}function
routine_languages(){return
array();}function
begin(){return
queries("BEGIN");}function
insert_into($O,$M){return
queries("INSERT INTO ".table($O)." (".implode(", ",array_keys($M)).")\nVALUES (".implode(", ",$M).")");}function
insert_update($O,$M,$Ke){foreach($M
as$w=>$W)$M[$w]="$w = $W";$Dg=implode(", ",$M);return
queries("INSERT INTO ".table($O)." SET $Dg ON DUPLICATE KEY UPDATE $Dg");}function
last_id(){global$h;return$h->result("SELECT LAST_INSERT_ID()");}function
explain($h,$F){return$h->query("EXPLAIN ".($h->server_info>=5.1?"PARTITIONS ":"").$F);}function
found_rows($P,$Z){return($Z||$P["Engine"]!="InnoDB"?null:$P["Rows"]);}function
types(){return
array();}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($rf){return
true;}function
create_sql($O,$za){global$h;$H=$h->result("SHOW CREATE TABLE ".table($O),1);if(!$za)$H=preg_replace('~ AUTO_INCREMENT=\\d+~','',$H);return$H;}function
truncate_sql($O){return"TRUNCATE ".table($O);}function
use_sql($ob){return"USE ".idf_escape($ob);}function
trigger_sql($O,$Jf){$H="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($O,"%_\\")),null,"-- ")as$I)$H.="\n".($Jf=='CREATE+ALTER'?"DROP TRIGGER IF EXISTS ".idf_escape($I["Trigger"]).";;\n":"")."CREATE TRIGGER ".idf_escape($I["Trigger"])." $I[Timing] $I[Event] ON ".table($I["Table"])." FOR EACH ROW\n$I[Statement];;\n";return$H;}function
show_variables(){return
get_key_vals("SHOW VARIABLES");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
show_status(){return
get_key_vals("SHOW STATUS");}function
convert_field($m){if(ereg("binary",$m["type"]))return"HEX(".idf_escape($m["field"]).")";if($m["type"]=="bit")return"BIN(".idf_escape($m["field"])." + 0)";if(ereg("geometry|point|linestring|polygon",$m["type"]))return"AsWKT(".idf_escape($m["field"]).")";}function
unconvert_field($m,$H){if(ereg("binary",$m["type"]))$H="UNHEX($H)";if($m["type"]=="bit")return"CONV($H, 2, 10) + 0";if(ereg("geometry|point|linestring|polygon",$m["type"]))$H="GeomFromText($H)";return$H;}function
support($ic){global$h;return!ereg("scheme|sequence|type".($h->server_info<5.1?"|event|partitioning".($h->server_info<5?"|view|routine|trigger":""):""),$ic);}$v="sql";$T=array();$If=array();foreach(array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),)as$w=>$W){$T+=$W;$If[$w]=array_keys($W);}$Cg=array("unsigned","zerofill","unsigned zerofill");$ee=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");$zc=array("char_length","date","from_unixtime","lower","round","sec_to_time","time_to_sec","upper");$Dc=array("avg","count","count distinct","group_concat","max","min","sum");$Hb=array(array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",),array("(^|[^o])int|float|double|decimal"=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",));}define("SERVER",$_GET[DRIVER]);define("DB",$_GET["db"]);define("ME",preg_replace('~^[^?]*/([^?]*).*~','\\1',$_SERVER["REQUEST_URI"]).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));$ia="3.7.0";class
Adminer{var$operators;function
name(){return"<a href='http://www.adminer.org/' id='h1'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_session("pwds"));}function
permanentLogin(){return
password_file();}function
database(){return
DB;}function
databases($oc=true){return
get_databases($oc);}function
queryTimeout(){return
5;}function
headers(){return
true;}function
head(){return
true;}function
loginForm(){global$Ab;echo'<table cellspacing="0">
<tr><th>System<td>',html_select("auth[driver]",$Ab,DRIVER,"loginDriver(this);"),'<tr><th>Server<td><input name="auth[server]" value="',h(SERVER),'" title="hostname[:port]" placeholder="localhost" autocapitalize="off">
<tr><th>Username<td><input name="auth[username]" id="username" value="',h($_GET["username"]),'" autocapitalize="off">
<tr><th>Password<td><input type="password" name="auth[password]">
<tr><th>Database<td><input name="auth[db]" value="',h($_GET["db"]);?>" autocapitalize="off">
</table>
<script type="text/javascript">
var username = document.getElementById('username');
focus(username);
username.form['auth[driver]'].onchange();
</script>
<?php

echo"<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
login($sd,$D){return
true;}function
tableName($Pf){return
h($Pf["Name"]);}function
fieldName($m,$ie=0){return'<span title="'.h($m["full_type"]).'">'.h($m["field"]).'</span>';}function
selectLinks($Pf,$M=""){echo'<p class="tabs">';$rd=array("select"=>'Select data',"table"=>'Show structure');if(is_view($Pf))$rd["view"]='Alter view';else$rd["create"]='Alter table';if($M!==null)$rd["edit"]='New item';foreach($rd
as$w=>$W)echo" <a href='".h(ME)."$w=".urlencode($Pf["Name"]).($w=="edit"?$M:"")."'".bold(isset($_GET[$w])).">$W</a>";echo"\n";}function
foreignKeys($O){return
foreign_keys($O);}function
backwardKeys($O,$Of){return
array();}function
backwardKeysPrint($Ba,$I){}function
selectQuery($F){global$v,$R;return"<form action='".h(ME)."sql=' method='post'><p><span onclick=\"return !selectEditSql(event, this, '".'Execute'."');\">"."<code class='jush-$v'>".h(str_replace("\n"," ",$F))."</code>"." <a href='".h(ME)."sql=".urlencode($F)."'>".'Edit'."</a>"."</span><input type='hidden' name='token' value='$R'></p></form>\n";}function
rowDescription($O){return"";}function
rowDescriptions($J,$qc){return$J;}function
selectLink($W,$m){}function
selectVal($W,$y,$m){$H=($W===null?"<i>NULL</i>":(ereg("char|binary",$m["type"])&&!ereg("var",$m["type"])?"<code>$W</code>":$W));if(ereg('blob|bytea|raw|file',$m["type"])&&!is_utf8($W))$H=lang(array('%d byte','%d bytes'),strlen(html_entity_decode($W,ENT_QUOTES)));return($y?"<a href='".h($y)."'>$H</a>":$H);}function
editVal($W,$m){return$W;}function
selectColumnsPrint($K,$g){global$zc,$Dc;print_fieldset("select",'Select',$K);$q=0;$xc=array('Functions'=>$zc,'Aggregation'=>$Dc);foreach($K
as$w=>$W){$W=$_GET["columns"][$w];echo"<div>".html_select("columns[$q][fun]",array(-1=>"")+$xc,$W["fun"]),"(<select name='columns[$q][col]' onchange='selectFieldChange(this.form);'><option>".optionlist($g,$W["col"],true)."</select>)</div>\n";$q++;}echo"<div>".html_select("columns[$q][fun]",array(-1=>"")+$xc,"","this.nextSibling.nextSibling.onchange();"),"(<select name='columns[$q][col]' onchange='selectAddRow(this);'><option>".optionlist($g,null,true)."</select>)</div>\n","</div></fieldset>\n";}function
selectSearchPrint($Z,$g,$u){print_fieldset("search",'Search',$Z);foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT"){echo"(<i>".implode("</i>, <i>",array_map('h',$t["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$q]' value='".h($_GET["fulltext"][$q])."' onchange='selectFieldChange(this.form);'>",checkbox("boolean[$q]",1,isset($_GET["boolean"][$q]),"BOOL"),"<br>\n";}}$_GET["where"]=(array)$_GET["where"];reset($_GET["where"]);$La="this.nextSibling.onchange();";for($q=0;$q<=count($_GET["where"]);$q++){list(,$W)=each($_GET["where"]);if(!$W||("$W[col]$W[val]"!=""&&in_array($W["op"],$this->operators))){echo"<div><select name='where[$q][col]' onchange='$La'><option value=''>(".'anywhere'.")".optionlist($g,$W["col"],true)."</select>",html_select("where[$q][op]",$this->operators,$W["op"],$La),"<input type='search' name='where[$q][val]' value='".h($W["val"])."' onchange='".($W?"selectFieldChange(this.form)":"selectAddRow(this)").";' onsearch='selectSearchSearch(this);'></div>\n";}}echo"</div></fieldset>\n";}function
selectOrderPrint($ie,$g,$u){print_fieldset("sort",'Sort',$ie);$q=0;foreach((array)$_GET["order"]as$w=>$W){if(isset($g[$W])){echo"<div><select name='order[$q]' onchange='selectFieldChange(this.form);'><option>".optionlist($g,$W,true)."</select>",checkbox("desc[$q]",1,isset($_GET["desc"][$w]),'descending')."</div>\n";$q++;}}echo"<div><select name='order[$q]' onchange='selectAddRow(this);'><option>".optionlist($g,null,true)."</select>","<label><input type='checkbox' name='desc[$q]' value='1'>".'descending'."</label></div>\n";echo"</div></fieldset>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".'Limit'."</legend><div>";echo"<input type='number' name='limit' class='size' value='".h($x)."' onchange='selectFieldChange(this.form);'>","</div></fieldset>\n";}function
selectLengthPrint($cg){if($cg!==null){echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($cg)."'>","</div></fieldset>\n";}}function
selectActionPrint($u){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script type='text/javascript'>\n","var indexColumns = ";$g=array();foreach($u
as$t){if($t["type"]!="FULLTEXT")$g[reset($t["columns"])]=1;}$g[""]=1;foreach($g
as$w=>$W)json_row($w);echo";\n","selectFieldChange(document.getElementById('form'));\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint($Lb,$g){}function
selectColumnsProcess($g,$u){global$zc,$Dc;$K=array();$Bc=array();foreach((array)$_GET["columns"]as$w=>$W){if($W["fun"]=="count"||(isset($g[$W["col"]])&&(!$W["fun"]||in_array($W["fun"],$zc)||in_array($W["fun"],$Dc)))){$K[$w]=apply_sql_function($W["fun"],(isset($g[$W["col"]])?idf_escape($W["col"]):"*"));if(!in_array($W["fun"],$Dc))$Bc[]=$K[$w];}}return
array($K,$Bc);}function
selectSearchProcess($n,$u){global$v;$H=array();foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT"&&$_GET["fulltext"][$q]!="")$H[]="MATCH (".implode(", ",array_map('idf_escape',$t["columns"])).") AGAINST (".q($_GET["fulltext"][$q]).(isset($_GET["boolean"][$q])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$W){if("$W[col]$W[val]"!=""&&in_array($W["op"],$this->operators)){$ab=" $W[op]";if(ereg('IN$',$W["op"])){$Mc=process_length($W["val"]);$ab.=" (".($Mc!=""?$Mc:"NULL").")";}elseif($W["op"]=="SQL")$ab=" $W[val]";elseif($W["op"]=="LIKE %%")$ab=" LIKE ".$this->processInput($n[$W["col"]],"%$W[val]%");elseif(!ereg('NULL$',$W["op"]))$ab.=" ".$this->processInput($n[$W["col"]],$W["val"]);if($W["col"]!="")$H[]=idf_escape($W["col"]).$ab;else{$Va=array();foreach($n
as$A=>$m){$Xc=ereg('char|text|enum|set',$m["type"]);if((is_numeric($W["val"])||!ereg('(^|[^o])int|float|double|decimal|bit',$m["type"]))&&(!ereg("[\x80-\xFF]",$W["val"])||$Xc)){$A=idf_escape($A);$Va[]=($v=="sql"&&$Xc&&!ereg('^utf8',$m["collation"])?"CONVERT($A USING utf8)":$A);}}$H[]=($Va?"(".implode("$ab OR ",$Va)."$ab)":"0");}}}return$H;}function
selectOrderProcess($n,$u){$H=array();foreach((array)$_GET["order"]as$w=>$W){if(isset($n[$W])||preg_match('~^((COUNT\\(DISTINCT |[A-Z0-9_]+\\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\\)|COUNT\\(\\*\\))$~',$W))$H[]=(isset($n[$W])?idf_escape($W):$W).(isset($_GET["desc"][$w])?" DESC":"");}return$H;}function
selectLimitProcess(){return(isset($_GET["limit"])?$_GET["limit"]:"50");}function
selectLengthProcess(){return(isset($_GET["text_length"])?$_GET["text_length"]:"100");}function
selectEmailProcess($Z,$qc){return
false;}function
selectQueryBuild($K,$Z,$Bc,$ie,$x,$C){return"";}function
messageQuery($F){global$v;restart_session();$Gc=&get_session("queries");$r="sql-".count($Gc[$_GET["db"]]);if(strlen($F)>1e6)$F=ereg_replace('[\x80-\xFF]+$','',substr($F,0,1e6))."\n...";$Gc[$_GET["db"]][]=array($F,time());return" <span class='time'>".@date("H:i:s")."</span> <a href='#$r' onclick=\"return !toggle('$r');\">".'SQL command'."</a><div id='$r' class='hidden'><pre><code class='jush-$v'>".shorten_utf8($F,1000).'</code></pre><p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($Gc[$_GET["db"]])-1)).'">'.'Edit'.'</a></div>';}function
editFunctions($m){global$Hb;$H=($m["null"]?"NULL/":"");foreach($Hb
as$w=>$zc){if(!$w||(!isset($_GET["call"])&&(isset($_GET["select"])||where($_GET)))){foreach($zc
as$Be=>$W){if(!$Be||ereg($Be,$m["type"]))$H.="/$W";}if($w&&!ereg('set|blob|bytea|raw|file',$m["type"]))$H.="/SQL";}}return
explode("/",$H);}function
editInput($O,$m,$ya,$X){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$ya value='-1' checked><i>".'original'."</i></label> ":"").($m["null"]?"<label><input type='radio'$ya value=''".($X!==null||isset($_GET["select"])?"":" checked")."><i>NULL</i></label> ":"").enum_input("radio",$ya,$m,$X,0);return"";}function
processInput($m,$X,$p=""){if($p=="SQL")return$X;$A=$m["field"];$H=q($X);if(ereg('^(now|getdate|uuid)$',$p))$H="$p()";elseif(ereg('^current_(date|timestamp)$',$p))$H=$p;elseif(ereg('^([+-]|\\|\\|)$',$p))$H=idf_escape($A)." $p $H";elseif(ereg('^[+-] interval$',$p))$H=idf_escape($A)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+$~i",$X)?$X:$H);elseif(ereg('^(addtime|subtime|concat)$',$p))$H="$p(".idf_escape($A).", $H)";elseif(ereg('^(md5|sha1|password|encrypt)$',$p))$H="$p($H)";return
unconvert_field($m,$H);}function
dumpOutput(){$H=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$H['gz']='gzip';return$H;}function
dumpFormat(){return
array('sql'=>'SQL','csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($k){}function
dumpTable($O,$Jf,$Yc=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Jf)dump_csv(array_keys(fields($O)));}elseif($Jf){if($Yc==2){$n=array();foreach(fields($O)as$A=>$m)$n[]=idf_escape($A)." $m[full_type]";$hb="CREATE TABLE ".table($O)." (".implode(", ",$n).")";}else$hb=create_sql($O,$_POST["auto_increment"]);if($hb){if($Jf=="DROP+CREATE"||$Yc==1)echo"DROP ".($Yc==2?"VIEW":"TABLE")." IF EXISTS ".table($O).";\n";if($Yc==1)$hb=remove_definer($hb);echo"$hb;\n\n";}}}function
dumpData($O,$Jf,$F){global$h,$v;$xd=($v=="sqlite"?0:1048576);if($Jf){if($_POST["format"]=="sql"){if($Jf=="TRUNCATE+INSERT")echo
truncate_sql($O).";\n";$n=fields($O);}$G=$h->query($F,1);if($G){$Tc="";$Ja="";$dd=array();$Lf="";$jc=($O!=''?'fetch_assoc':'fetch_row');while($I=$G->$jc()){if(!$dd){$Kg=array();foreach($I
as$W){$m=$G->fetch_field();$dd[]=$m->name;$w=idf_escape($m->name);$Kg[]="$w = VALUES($w)";}$Lf=($Jf=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Kg):"").";\n";}if($_POST["format"]!="sql"){if($Jf=="table"){dump_csv($dd);$Jf="INSERT";}dump_csv($I);}else{if(!$Tc)$Tc="INSERT INTO ".table($O)." (".implode(", ",array_map('idf_escape',$dd)).") VALUES";foreach($I
as$w=>$W){$m=$n[$w];$I[$w]=($W!==null?unconvert_field($m,ereg('(^|[^o])int|float|double|decimal',$m["type"])&&$W!=''?$W:q($W)):"NULL");}$pf=($xd?"\n":" ")."(".implode(",\t",$I).")";if(!$Ja)$Ja=$Tc.$pf;elseif(strlen($Ja)+4+strlen($pf)+strlen($Lf)<$xd)$Ja.=",$pf";else{echo$Ja.$Lf;$Ja=$Tc.$pf;}}}if($Ja)echo$Ja.$Lf;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",$h->error)."\n";}}function
dumpFilename($Kc){return
friendly_url($Kc!=""?$Kc:(SERVER!=""?SERVER:"localhost"));}function
dumpHeaders($Kc,$Kd=false){$se=$_POST["output"];$dc=(ereg('sql',$_POST["format"])?"sql":($Kd?"tar":"csv"));header("Content-Type: ".($se=="gz"?"application/x-gzip":($dc=="tar"?"application/x-tar":($dc=="sql"||$se!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($se=="gz")ob_start('gzencode',1e6);return$dc;}function
homepage(){echo'<p>'.($_GET["ns"]==""?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");return
true;}function
navigation($Jd){global$ia,$R,$v,$Ab;echo'<h1>
',$this->name(),' <span class="version">',$ia,'</span>
<a href="http://www.adminer.org/#download" id="version">',(version_compare($ia,$_COOKIE["adminer_version"])<0?h($_COOKIE["adminer_version"]):""),'</a>
</h1>
';if($Jd=="auth"){$nc=true;foreach((array)$_SESSION["pwds"]as$_b=>$zf){foreach($zf
as$L=>$Ig){foreach($Ig
as$U=>$D){if($D!==null){if($nc){echo"<p id='logins' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>\n";$nc=false;}$qb=$_SESSION["db"][$_b][$L][$U];foreach(($qb?array_keys($qb):array(""))as$k)echo"<a href='".h(auth_url($_b,$L,$U,$k))."'>($Ab[$_b]) ".h($U.($L!=""?"@$L":"").($k!=""?" - $k":""))."</a><br>\n";}}}}}else{echo'<form action="" method="post">
<p class="logout">
';if(DB==""||!$Jd){echo"<a href='".h(ME)."sql='".bold(isset($_GET["sql"])).">".'SQL command'."</a>\n";if(support("dump"))echo"<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Dump'."</a>\n";}echo'<input type="submit" name="logout" value="Logout" id="logout">
<input type="hidden" name="token" value="',$R,'">
</p>
</form>
';$this->databasesPrint($Jd);if($_GET["ns"]!==""&&!$Jd&&DB!=""){echo'<p><a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create new table'."</a>\n";$Q=table_status('',true);if(!$Q)echo"<p class='message'>".'No tables.'."\n";else{$this->tablesPrint($Q);$rd=array();foreach($Q
as$O=>$S)$rd[]=preg_quote($O,'/');echo"<script type='text/javascript'>\n","var jushLinks = { $v: [ '".js_escape(ME)."table=\$&', /\\b(".implode("|",$rd).")\\b/g ] };\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$W)echo"jushLinks.$W = jushLinks.$v;\n";echo"</script>\n";}}}}function
databasesPrint($Jd){global$h;$j=$this->databases();echo'<form action="">
<p id="dbs">
';hidden_fields_get();echo($j?'<select name="db" onmousedown="dbMouseDown(event, this);" onchange="dbChange(this);">'.optionlist(array(""=>"(".'database'.")")+$j,DB).'</select>':'<input name="db" value="'.h(DB).'" autocapitalize="off">'),'<input type="submit" value="Use"',($j?" class='hidden'":""),'>
';if($Jd!="db"&&DB!=""&&$h->select_db(DB)){if(support("scheme")){echo"<br>".html_select("ns",array(""=>"(".'schema'.")")+schemas(),$_GET["ns"],"this.form.submit();");if($_GET["ns"]!="")set_schema($_GET["ns"]);}}echo(isset($_GET["sql"])?'<input type="hidden" name="sql" value="">':(isset($_GET["schema"])?'<input type="hidden" name="schema" value="">':(isset($_GET["dump"])?'<input type="hidden" name="dump" value="">':""))),"</p></form>\n";}function
tablesPrint($Q){echo"<p id='tables' onmouseover='menuOver(this, event);' onmouseout='menuOut(this);'>\n";foreach($Q
as$O=>$Ff){echo'<a href="'.h(ME).'select='.urlencode($O).'"'.bold($_GET["select"]==$O).">".'select'."</a> ",'<a href="'.h(ME).'table='.urlencode($O).'"'.bold($_GET["table"]==$O)." title='".'Show structure'."'>".$this->tableName($Ff)."</a><br>\n";}}}$b=(function_exists('adminer_object')?adminer_object():new
Adminer);if($b->operators===null)$b->operators=$ee;function
page_header($fg,$l="",$Ia=array(),$gg=""){global$ca,$b,$h,$Ab;header("Content-Type: text/html; charset=utf-8");if($b->headers()){header("X-Frame-Options: deny");header("X-XSS-Protection: 0");}$hg=$fg.($gg!=""?": ".h($gg):"");$ig=strip_tags($hg.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".$b->name());echo'<!DOCTYPE html>
<html lang="en" dir="ltr">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<meta name="robots" content="noindex">
<title>',$ig,'</title>
<link rel="stylesheet" type="text/css" href="',h(preg_replace("~\\?.*~","",ME))."?file=default.css&amp;version=3.7.0",'">
<script type="text/javascript" src="',h(preg_replace("~\\?.*~","",ME))."?file=functions.js&amp;version=3.7.0",'"></script>
';if($b->head()){echo'<link rel="shortcut icon" type="image/x-icon" href="',h(preg_replace("~\\?.*~","",ME))."?file=favicon.ico&amp;version=3.7.0",'">
<link rel="apple-touch-icon" href="',h(preg_replace("~\\?.*~","",ME))."?file=favicon.ico&amp;version=3.7.0",'">
';if(file_exists("adminer.css")){echo'<link rel="stylesheet" type="text/css" href="adminer.css">
';}}echo'
<body class="ltr nojs" onkeydown="bodyKeydown(event);" onclick="bodyClick(event);" onload="bodyLoad(\'',(is_object($h)?substr($h->server_info,0,3):""),'\');',(isset($_COOKIE["adminer_version"])?"":" verifyVersion();"),'">
<script type="text/javascript">
document.body.className = document.body.className.replace(/ nojs/, \' js\');
</script>

<div id="content">
';if($Ia!==null){$y=substr(preg_replace('~(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($y?$y:".").'">'.$Ab[DRIVER].'</a> &raquo; ';$y=substr(preg_replace('~(db|ns)=[^&]*&~','',ME),0,-1);$L=(SERVER!=""?h(SERVER):'Server');if($Ia===false)echo"$L\n";else{echo"<a href='".($y?h($y):".")."' accesskey='1' title='Alt+Shift+1'>$L</a> &raquo; ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ia)))echo'<a href="'.h($y."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> &raquo; ';if(is_array($Ia)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> &raquo; ';foreach($Ia
as$w=>$W){$ub=(is_array($W)?$W[1]:$W);if($ub!="")echo'<a href="'.h(ME."$w=").urlencode(is_array($W)?$W[0]:$W).'">'.h($ub).'</a> &raquo; ';}}echo"$fg\n";}}echo"<h2>$hg</h2>\n";restart_session();$Eg=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Gd=$_SESSION["messages"][$Eg];if($Gd){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Gd)."</div>\n";unset($_SESSION["messages"][$Eg]);}$j=&get_session("dbs");if(DB!=""&&$j&&!in_array(DB,$j,true))$j=null;stop_session();if($l)echo"<div class='error'>$l</div>\n";define("PAGE_HEADER",1);}function
page_footer($Jd=""){global$b;echo'</div>

<div id="menu">
';$b->navigation($Jd);echo'</div>
<script type="text/javascript">setupSubmitHighlight(document);</script>
';}function
int32($Md){while($Md>=2147483648)$Md-=4294967296;while($Md<=-2147483649)$Md+=4294967296;return(int)$Md;}function
long2str($V,$Pg){$pf='';foreach($V
as$W)$pf.=pack('V',$W);if($Pg)return
substr($pf,0,end($V));return$pf;}function
str2long($pf,$Pg){$V=array_values(unpack('V*',str_pad($pf,4*ceil(strlen($pf)/4),"\0")));if($Pg)$V[]=strlen($pf);return$V;}function
xxtea_mx($Ug,$Tg,$Mf,$bd){return
int32((($Ug>>5&0x7FFFFFF)^$Tg<<2)+(($Tg>>3&0x1FFFFFFF)^$Ug<<4))^int32(($Mf^$Tg)+($bd^$Ug));}function
encrypt_string($Hf,$w){if($Hf=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$V=str2long($Hf,true);$Md=count($V)-1;$Ug=$V[$Md];$Tg=$V[0];$E=floor(6+52/($Md+1));$Mf=0;while($E-->0){$Mf=int32($Mf+0x9E3779B9);$Gb=$Mf>>2&3;for($te=0;$te<$Md;$te++){$Tg=$V[$te+1];$Ld=xxtea_mx($Ug,$Tg,$Mf,$w[$te&3^$Gb]);$Ug=int32($V[$te]+$Ld);$V[$te]=$Ug;}$Tg=$V[0];$Ld=xxtea_mx($Ug,$Tg,$Mf,$w[$te&3^$Gb]);$Ug=int32($V[$Md]+$Ld);$V[$Md]=$Ug;}return
long2str($V,false);}function
decrypt_string($Hf,$w){if($Hf=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$V=str2long($Hf,false);$Md=count($V)-1;$Ug=$V[$Md];$Tg=$V[0];$E=floor(6+52/($Md+1));$Mf=int32($E*0x9E3779B9);while($Mf){$Gb=$Mf>>2&3;for($te=$Md;$te>0;$te--){$Ug=$V[$te-1];$Ld=xxtea_mx($Ug,$Tg,$Mf,$w[$te&3^$Gb]);$Tg=int32($V[$te]-$Ld);$V[$te]=$Tg;}$Ug=$V[$Md];$Ld=xxtea_mx($Ug,$Tg,$Mf,$w[$te&3^$Gb]);$Tg=int32($V[0]-$Ld);$V[0]=$Tg;$Mf=int32($Mf-0x9E3779B9);}return
long2str($V,true);}$h='';$R=$_SESSION["token"];if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);$Ce=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($w)=explode(":",$W);$Ce[$w]=$W;}}$d=$_POST["auth"];if($d){session_regenerate_id();$_SESSION["pwds"][$d["driver"]][$d["server"]][$d["username"]]=$d["password"];$_SESSION["db"][$d["driver"]][$d["server"]][$d["username"]][$d["db"]]=true;if($d["permanent"]){$w=base64_encode($d["driver"])."-".base64_encode($d["server"])."-".base64_encode($d["username"])."-".base64_encode($d["db"]);$Ne=$b->permanentLogin();$Ce[$w]="$w:".base64_encode($Ne?encrypt_string($d["password"],$Ne):"");cookie("adminer_permanent",implode(" ",$Ce));}if(count($_POST)==1||DRIVER!=$d["driver"]||SERVER!=$d["server"]||$_GET["username"]!==$d["username"]||DB!=$d["db"])redirect(auth_url($d["driver"],$d["server"],$d["username"],$d["db"]));}elseif($_POST["logout"]){if($R&&$_POST["token"]!=$R){page_header('Logout','Invalid CSRF token. Send the form again.');page_footer("db");exit;}else{foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent();redirect(substr(preg_replace('~(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.');}}elseif($Ce&&!$_SESSION["pwds"]){session_regenerate_id();$Ne=$b->permanentLogin();foreach($Ce
as$w=>$W){list(,$Pa)=explode(":",$W);list($_b,$L,$U,$k)=array_map('base64_decode',explode("-",$w));$_SESSION["pwds"][$_b][$L][$U]=decrypt_string(base64_decode($Pa),$Ne);$_SESSION["db"][$_b][$L][$U][$k]=true;}}function
unset_permanent(){global$Ce;foreach($Ce
as$w=>$W){list($_b,$L,$U,$k)=array_map('base64_decode',explode("-",$w));if($_b==DRIVER&&$L==SERVER&&$U==$_GET["username"]&&$k==DB)unset($Ce[$w]);}cookie("adminer_permanent",implode(" ",$Ce));}function
auth_error($Xb=null){global$h,$b,$R;$_f=session_name();$l="";if(!$_COOKIE[$_f]&&$_GET[$_f]&&ini_bool("session.use_only_cookies"))$l='Session support must be enabled.';elseif(isset($_GET["username"])){if(($_COOKIE[$_f]||$_GET[$_f])&&!$R)$l='Session expired, please login again.';else{$D=&get_session("pwds");if($D!==null){$l=h($Xb?$Xb->getMessage():(is_string($h)?$h:'Invalid credentials.'));$D=null;}unset_permanent();}}page_header('Login',$l,null);echo"<form action='' method='post'>\n";$b->loginForm();echo"<div>";hidden_fields($_POST,array("auth"));echo"</div>\n","</form>\n";page_footer("auth");}if(isset($_GET["username"])){if(!class_exists("Min_DB")){unset($_SESSION["pwds"][DRIVER]);unset_permanent();page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",$He)),false);page_footer("auth");exit;}$h=connect();}if(is_string($h)||!$b->login($_GET["username"],get_session("pwds"))){auth_error();exit;}$R=$_SESSION["token"];if($d&&$_POST["token"])$_POST["token"]=$R;$l='';if($_POST){if($_POST["token"]!=$R){$Qc="max_input_vars";$Ad=ini_get($Qc);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$w){$W=ini_get($w);if($W&&(!$Ad||$W<$Ad)){$Qc=$w;$Ad=$W;}}}$l=(!$_POST["token"]&&$Ad?sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"'$Qc'"):'Invalid CSRF token. Send the form again.');}}elseif($_SERVER["REQUEST_METHOD"]=="POST")$l=sprintf('Too big POST data. Reduce the data or increase the %s configuration directive.',"'post_max_size'");if(!ini_bool("session.use_cookies")||@ini_set("session.use_cookies",false)!==false){session_cache_limiter("");session_write_close();}function
connect_error(){global$b,$h,$R,$l,$Ab;$j=array();if(DB!="")page_header('Database'.": ".h(DB),'Invalid database.',true);else{if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$l,false);echo"<p><a href='".h(ME)."database='>".'Create new database'."</a>\n";foreach(array('privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$w=>$W){if(support($w))echo"<a href='".h(ME)."$w='>$W</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',$Ab[DRIVER],"<b>$h->server_info</b>","<b>$h->extension</b>")."\n","<p>".sprintf('Logged as: %s',"<b>".h(logged_user())."</b>")."\n";$bf="<a href='".h(ME)."refresh=1'>".'Refresh'."</a>\n";$j=$b->databases();if($j){$sf=support("scheme");$Ua=collations();echo"<form action='' method='post'>\n","<table cellspacing='0' class='checkable' onclick='tableClick(event);' ondblclick='tableClick(event, true);'>\n","<thead><tr><td>&nbsp;<th>".'Database'."<td>".'Collation'."<td>".'Tables'."</thead>\n";foreach($j
as$k){$kf=h(ME)."db=".urlencode($k);echo"<tr".odd()."><td>".checkbox("db[]",$k,in_array($k,(array)$_POST["db"])),"<th><a href='$kf'>".h($k)."</a>","<td><a href='$kf".($sf?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>".nbsp(db_collation($k,$Ua))."</a>","<td align='right'><a href='$kf&amp;schema=' id='tables-".h($k)."' title='".'Database schema'."'>?</a>","\n";}echo"</table>\n","<script type='text/javascript'>tableCheck();</script>\n","<p><input type='submit' name='drop' value='".'Drop'."'".confirm("formChecked(this, /db/)").">\n","<input type='hidden' name='token' value='$R'>\n",$bf,"</form>\n";}else
echo"<p>$bf";}page_footer("db");if($j)echo"<script type='text/javascript'>ajaxSetHtml('".js_escape(ME)."script=connect');</script>\n";}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(!(DB!=""?$h->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}connect_error();exit;}if(support("scheme")&&DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~ns=[^&]*&~','',ME)."ns=".get_schema());if(!set_schema($_GET["ns"])){page_header('Schema'.": ".h($_GET["ns"]),'Invalid schema.',true);page_footer("ns");exit;}}function
select($G,$i=null,$Jc="",$le=array()){$rd=array();$u=array();$g=array();$Ga=array();$T=array();$H=array();odd('');for($q=0;$I=$G->fetch_row();$q++){if(!$q){echo"<table cellspacing='0' class='nowrap'>\n","<thead><tr>";for($Zc=0;$Zc<count($I);$Zc++){$m=$G->fetch_field();$A=$m->name;$ke=$m->orgtable;$je=$m->orgname;$H[$m->table]=$ke;if($Jc)$rd[$Zc]=($A=="table"?"table=":($A=="possible_keys"?"indexes=":null));elseif($ke!=""){if(!isset($u[$ke])){$u[$ke]=array();foreach(indexes($ke,$i)as$t){if($t["type"]=="PRIMARY"){$u[$ke]=array_flip($t["columns"]);break;}}$g[$ke]=$u[$ke];}if(isset($g[$ke][$je])){unset($g[$ke][$je]);$u[$ke][$je]=$Zc;$rd[$Zc]=$ke;}}if($m->charsetnr==63)$Ga[$Zc]=true;$T[$Zc]=$m->type;$A=h($A);echo"<th".($ke!=""||$m->name!=$je?" title='".h(($ke!=""?"$ke.":"").$je)."'":"").">".($Jc?"<a href='$Jc".strtolower($A)."' target='_blank' rel='noreferrer' class='help'>$A</a>":$A);}echo"</thead>\n";}echo"<tr".odd().">";foreach($I
as$w=>$W){if($W===null)$W="<i>NULL</i>";elseif($Ga[$w]&&!is_utf8($W))$W="<i>".lang(array('%d byte','%d bytes'),strlen($W))."</i>";elseif(!strlen($W))$W="&nbsp;";else{$W=h($W);if($T[$w]==254)$W="<code>$W</code>";}if(isset($rd[$w])&&!$g[$rd[$w]]){if($Jc){$O=$I[array_search("table=",$rd)];$y=$rd[$w].urlencode($le[$O]!=""?$le[$O]:$O);}else{$y="edit=".urlencode($rd[$w]);foreach($u[$rd[$w]]as$Sa=>$Zc)$y.="&where".urlencode("[".bracket_escape($Sa)."]")."=".urlencode($I[$Zc]);}$W="<a href='".h(ME.$y)."'>$W</a>";}echo"<td>$W";}}echo($q?"</table>":"<p class='message'>".'No rows.')."\n";return$H;}function
referencable_primary($vf){$H=array();foreach(table_status('',true)as$Qf=>$O){if($Qf!=$vf&&fk_support($O)){foreach(fields($Qf)as$m){if($m["primary"]){if($H[$Qf]){unset($H[$Qf]);break;}$H[$Qf]=$m;}}}}return$H;}function
textarea($A,$X,$J=10,$Va=80){echo"<textarea name='$A' rows='$J' cols='$Va' class='sqlarea' spellcheck='false' wrap='off' onkeydown='return textareaKeydown(this, event);'>";if(is_array($X)){foreach($X
as$W)echo
h($W[0])."\n\n\n";}else
echo
h($X);echo"</textarea>";}function
edit_type($w,$m,$Ua,$rc=array()){global$If,$T,$Cg,$ae;echo'<td><select name="',$w,'[type]" class="type" onfocus="lastType = selectValue(this);" onchange="editingTypeChange(this);">',optionlist((!$m["type"]||isset($T[$m["type"]])?array():array($m["type"]))+$If+($rc?array('Foreign keys'=>$rc):array()),$m["type"]),'</select>
<td><input name="',$w,'[length]" value="',h($m["length"]),'" size="3" onfocus="editingLengthFocus(this);"><td class="options">';echo"<select name='$w"."[collation]'".(ereg('(char|text|enum|set)$',$m["type"])?"":" class='hidden'").'><option value="">('.'collation'.')'.optionlist($Ua,$m["collation"]).'</select>',($Cg?"<select name='$w"."[unsigned]'".(!$m["type"]||ereg('((^|[^o])int|float|double|decimal)$',$m["type"])?"":" class='hidden'").'><option>'.optionlist($Cg,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='$w"."[on_update]'".($m["type"]=="timestamp"?"":" class='hidden'").'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),$m["on_update"]).'</select>':''),($rc?"<select name='$w"."[on_delete]'".(ereg("`",$m["type"])?"":" class='hidden'")."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",$ae),$m["on_delete"])."</select> ":" ");}function
process_length($od){global$Rb;return(preg_match("~^\\s*(?:$Rb)(?:\\s*,\\s*(?:$Rb))*\\s*\$~",$od)&&preg_match_all("~$Rb~",$od,$vd)?implode(",",$vd[0]):preg_replace('~[^0-9,+-]~','',$od));}function
process_type($m,$Ta="COLLATE"){global$Cg;return" $m[type]".($m["length"]!=""?"(".process_length($m["length"]).")":"").(ereg('(^|[^o])int|float|double|decimal',$m["type"])&&in_array($m["unsigned"],$Cg)?" $m[unsigned]":"").(ereg('char|text|enum|set',$m["type"])&&$m["collation"]?" $Ta ".q($m["collation"]):"");}function
process_field($m,$vg){return
array(idf_escape(trim($m["field"])),process_type($vg),($m["null"]?" NULL":" NOT NULL"),(isset($m["default"])?" DEFAULT ".((ereg("time",$m["type"])&&eregi('^CURRENT_TIMESTAMP$',$m["default"]))||($m["type"]=="bit"&&ereg("^([0-9]+|b'[0-1]+')\$",$m["default"]))?$m["default"]:q($m["default"])):""),($m["type"]=="timestamp"&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
type_class($S){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$W){if(ereg("$w|$W",$S))return" class='$w'";}}function
edit_fields($n,$Ua,$S="TABLE",$rc=array(),$Za=false){global$h,$Rc;echo'<thead><tr class="wrap">
';if($S=="PROCEDURE"){echo'<td>&nbsp;';}echo'<th>',($S=="TABLE"?'Column name':'Parameter name'),'<td>Type<textarea id="enum-edit" rows="4" cols="12" wrap="off" style="display: none;" onblur="editingLengthBlur(this);"></textarea>
<td>Length
<td>Options
';if($S=="TABLE"){echo'<td>NULL
<td><input type="radio" name="auto_increment_col" value=""><acronym title="Auto Increment">AI</acronym>
<td>Default values
',(support("comment")?"<td".($Za?"":" class='hidden'").">".'Comment':"");}echo'<td>',"<input type='image' class='icon' name='add[".(support("move_col")?0:count($n))."]' src='".h(preg_replace("~\\?.*~","",ME))."?file=plus.gif&amp;version=3.7.0' alt='+' title='".'Add next'."'>",'<script type="text/javascript">row_count = ',count($n),';</script>
</thead>
<tbody onkeydown="return editingKeydown(event);">
';foreach($n
as$q=>$m){$q++;$me=$m[($_POST?"orig":"field")];$yb=(isset($_POST["add"][$q-1])||(isset($m["field"])&&!$_POST["drop_col"][$q]))&&(support("drop_col")||$me=="");echo'<tr',($yb?"":" style='display: none;'"),'>
',($S=="PROCEDURE"?"<td>".html_select("fields[$q][inout]",explode("|",$Rc),$m["inout"]):""),'<th>';if($yb){echo'<input name="fields[',$q,'][field]" value="',h($m["field"]),'" onchange="',($m["field"]!=""||count($n)>1?"":"editingAddRow(this); "),'editingNameChange(this);" maxlength="64" autocapitalize="off">';}echo'<input type="hidden" name="fields[',$q,'][orig]" value="',h($me),'">
';edit_type("fields[$q]",$m,$Ua,$rc);if($S=="TABLE"){echo'<td>',checkbox("fields[$q][null]",1,$m["null"]),'<td><input type="radio" name="auto_increment_col" value="',$q,'"';if($m["auto_increment"]){echo' checked';}?> onclick="var field = this.form['fields[' + this.value + '][field]']; if (!field.value) { field.value = 'id'; field.onchange(); }">
<td><?php echo
checkbox("fields[$q][has_default]",1,$m["has_default"]),'<input name="fields[',$q,'][default]" value="',h($m["default"]),'" onchange="this.previousSibling.checked = true;">
',(support("comment")?"<td".($Za?"":" class='hidden'")."><input name='fields[$q][comment]' value='".h($m["comment"])."' maxlength='".($h->server_info>=5.5?1024:255)."'>":"");}echo"<td>",(support("move_col")?"<input type='image' class='icon' name='add[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=plus.gif&amp;version=3.7.0' alt='+' title='".'Add next'."' onclick='return !editingAddRow(this, 1);'>&nbsp;"."<input type='image' class='icon' name='up[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=up.gif&amp;version=3.7.0' alt='^' title='".'Move up'."'>&nbsp;"."<input type='image' class='icon' name='down[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=down.gif&amp;version=3.7.0' alt='v' title='".'Move down'."'>&nbsp;":""),($me==""||support("drop_col")?"<input type='image' class='icon' name='drop_col[$q]' src='".h(preg_replace("~\\?.*~","",ME))."?file=cross.gif&amp;version=3.7.0' alt='x' title='".'Remove'."' onclick='return !editingRemoveRow(this);'>":""),"\n";}}function
process_fields(&$n){ksort($n);$B=0;if($_POST["up"]){$id=0;foreach($n
as$w=>$m){if(key($_POST["up"])==$w){unset($n[$w]);array_splice($n,$id,0,array($m));break;}if(isset($m["field"]))$id=$B;$B++;}}elseif($_POST["down"]){$tc=false;foreach($n
as$w=>$m){if(isset($m["field"])&&$tc){unset($n[key($_POST["down"])]);array_splice($n,$B,0,array($tc));break;}if(key($_POST["down"])==$w)$tc=$m;$B++;}}elseif($_POST["add"]){$n=array_values($n);array_splice($n,key($_POST["add"]),0,array(array()));}elseif(!$_POST["drop_col"])return
false;return
true;}function
normalize_enum($_){return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($_[0][0].$_[0][0],$_[0][0],substr($_[0],1,-1))),'\\'))."'";}function
grant($_c,$Pe,$g,$Zd){if(!$Pe)return
true;if($Pe==array("ALL PRIVILEGES","GRANT OPTION"))return($_c=="GRANT"?queries("$_c ALL PRIVILEGES$Zd WITH GRANT OPTION"):queries("$_c ALL PRIVILEGES$Zd")&&queries("$_c GRANT OPTION$Zd"));return
queries("$_c ".preg_replace('~(GRANT OPTION)\\([^)]*\\)~','\\1',implode("$g, ",$Pe).$g).$Zd);}function
drop_create($Bb,$hb,$Cb,$ag,$Db,$z,$Fd,$Dd,$Ed,$Wd,$Pd){if($_POST["drop"])query_redirect($Bb,$z,$Fd);elseif($Wd=="")query_redirect($hb,$z,$Ed);elseif($Wd!=$Pd){$jb=queries($hb);queries_redirect($z,$Dd,$jb&&queries($Bb));if($jb)queries($Cb);}else
queries_redirect($z,$Dd,queries($ag)&&queries($Db)&&queries($Bb)&&queries($hb));}function
create_trigger($Zd,$I){global$v;$eg=" $I[Timing] $I[Event]";return"CREATE TRIGGER ".idf_escape($I["Trigger"]).($v=="mssql"?$Zd.$eg:$eg.$Zd).rtrim(" $I[Type]\n$I[Statement]",";").";";}function
create_routine($lf,$I){global$Rc;$M=array();$n=(array)$I["fields"];ksort($n);foreach($n
as$m){if($m["field"]!="")$M[]=(ereg("^($Rc)\$",$m["inout"])?"$m[inout] ":"").idf_escape($m["field"]).process_type($m,"CHARACTER SET");}return"CREATE $lf ".idf_escape(trim($I["name"]))." (".implode(", ",$M).")".(isset($_GET["function"])?" RETURNS".process_type($I["returns"],"CHARACTER SET"):"").($I["language"]?" LANGUAGE $I[language]":"").rtrim("\n$I[definition]",";").";";}function
remove_definer($F){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\\1)',logged_user()).'`~','\\1',$F);}function
tar_file($lc,$jg){$H=pack("a100a8a8a8a12a12",$lc,644,0,0,decoct($jg->size),decoct(time()));$Oa=8*32;for($q=0;$q<strlen($H);$q++)$Oa+=ord($H[$q]);$H.=sprintf("%06o",$Oa)."\0 ";echo$H,str_repeat("\0",512-strlen($H));$jg->send();echo
str_repeat("\0",511-($jg->size+511)%512);}function
ini_bytes($Qc){$W=ini_get($Qc);switch(strtolower(substr($W,-1))){case'g':$W*=1024;case'm':$W*=1024;case'k':$W*=1024;}return$W;}$ae="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";class
TmpFile{var$handler;var$size;function
TmpFile(){$this->handler=tmpfile();}function
write($db){$this->size+=strlen($db);fwrite($this->handler,$db);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}$Rb="'(?:''|[^'\\\\]|\\\\.)*+'";$Rc="IN|OUT|INOUT";if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));echo$h->result("SELECT".limit(idf_escape($_GET["field"])." FROM ".table($a)," WHERE ".where($_GET,$n),1));exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=error();$P=table_status($a,true);page_header(($n&&is_view($P)?'View':'Table').": ".h($a),$l);$b->selectLinks($P);$Ya=$P["Comment"];if($Ya!="")echo"<p>".'Comment'.": ".h($Ya)."\n";if($n){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."</thead>\n";foreach($n
as$m){echo"<tr".odd()."><th>".h($m["field"]),"<td title='".h($m["collation"])."'>".h($m["full_type"]).($m["null"]?" <i>NULL</i>":"").($m["auto_increment"]?" <i>".'Auto Increment'."</i>":""),(isset($m["default"])?" [<b>".h($m["default"])."</b>]":""),(support("comment")?"<td>".nbsp($m["comment"]):""),"\n";}echo"</table>\n";if(!is_view($P)){echo"<h3 id='indexes'>".'Indexes'."</h3>\n";$u=indexes($a);if($u){echo"<table cellspacing='0'>\n";foreach($u
as$A=>$t){ksort($t["columns"]);$Me=array();foreach($t["columns"]as$w=>$W)$Me[]="<i>".h($W)."</i>".($t["lengths"][$w]?"(".$t["lengths"][$w].")":"");echo"<tr title='".h($A)."'><th>$t[type]<td>".implode(", ",$Me)."\n";}echo"</table>\n";}echo'<p><a href="'.h(ME).'indexes='.urlencode($a).'">'.'Alter indexes'."</a>\n";if(fk_support($P)){echo"<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$rc=foreign_keys($a);if($rc){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'.($v!="sqlite"?"<td>&nbsp;":"")."</thead>\n";foreach($rc
as$A=>$o){echo"<tr title='".h($A)."'>","<th><i>".implode("</i>, <i>",array_map('h',$o["source"]))."</i>","<td><a href='".h($o["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($o["db"]),ME):($o["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($o["ns"]),ME):ME))."table=".urlencode($o["table"])."'>".($o["db"]!=""?"<b>".h($o["db"])."</b>.":"").($o["ns"]!=""?"<b>".h($o["ns"])."</b>.":"").h($o["table"])."</a>","(<i>".implode("</i>, <i>",array_map('h',$o["target"]))."</i>)","<td>".nbsp($o["on_delete"])."\n","<td>".nbsp($o["on_update"])."\n",($v=="sqlite"?"":'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($A)).'">'.'Alter'.'</a>');}echo"</table>\n";}if($v!="sqlite")echo'<p><a href="'.h(ME).'foreign='.urlencode($a).'">'.'Add foreign key'."</a>\n";}if(support("trigger")){echo"<h3 id='triggers'>".'Triggers'."</h3>\n";$ug=triggers($a);if($ug){echo"<table cellspacing='0'>\n";foreach($ug
as$w=>$W)echo"<tr valign='top'><td>$W[0]<td>$W[1]<th>".h($w)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($w))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p><a href="'.h(ME).'trigger='.urlencode($a).'">'.'Add trigger'."</a>\n";}}}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),DB.($_GET["ns"]?".$_GET[ns]":""));$Sf=array();$Tf=array();$A="adminer_schema";$ea=($_GET["schema"]?$_GET["schema"]:$_COOKIE[($_COOKIE["$A-".DB]?"$A-".DB:$A)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ea,$vd,PREG_SET_ORDER);foreach($vd
as$q=>$_){$Sf[$_[1]]=array($_[2],$_[3]);$Tf[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$lg=0;$Da=-1;$rf=array();$af=array();$md=array();foreach(table_status('',true)as$O=>$P){if(is_view($P))continue;$Ee=0;$rf[$O]["fields"]=array();foreach(fields($O)as$A=>$m){$Ee+=1.25;$m["pos"]=$Ee;$rf[$O]["fields"][$A]=$m;}$rf[$O]["pos"]=($Sf[$O]?$Sf[$O]:array($lg,0));foreach($b->foreignKeys($O)as$W){if(!$W["db"]){$kd=$Da;if($Sf[$O][1]||$Sf[$W["table"]][1])$kd=min(floatval($Sf[$O][1]),floatval($Sf[$W["table"]][1]))-1;else$Da-=.1;while($md[(string)$kd])$kd-=.0001;$rf[$O]["references"][$W["table"]][(string)$kd]=array($W["source"],$W["target"]);$af[$W["table"]][$O][(string)$kd]=$W["target"];$md[(string)$kd]=true;}}$lg=max($lg,$rf[$O]["pos"][0]+2.5+$Ee);}echo'<div id="schema" style="height: ',$lg,'em;" onselectstart="return false;">
<script type="text/javascript">
var tablePos = {',implode(",",$Tf)."\n",'};
var em = document.getElementById(\'schema\').offsetHeight / ',$lg,';
document.onmousemove = schemaMousemove;
document.onmouseup = function (ev) {
	schemaMouseup(ev, \'',js_escape(DB),'\');
};
</script>
';foreach($rf
as$A=>$O){echo"<div class='table' style='top: ".$O["pos"][0]."em; left: ".$O["pos"][1]."em;' onmousedown='schemaMousedown(this, event);'>",'<a href="'.h(ME).'table='.urlencode($A).'"><b>'.h($A)."</b></a>";foreach($O["fields"]as$m){$W='<span'.type_class($m["type"]).' title="'.h($m["full_type"].($m["null"]?" NULL":'')).'">'.h($m["field"]).'</span>';echo"<br>".($m["primary"]?"<i>$W</i>":$W);}foreach((array)$O["references"]as$Yf=>$cf){foreach($cf
as$kd=>$Xe){$ld=$kd-$Sf[$A][1];$q=0;foreach($Xe[0]as$Bf)echo"\n<div class='references' title='".h($Yf)."' id='refs$kd-".($q++)."' style='left: $ld"."em; top: ".$O["fields"][$Bf]["pos"]."em; padding-top: .5em;'><div style='border-top: 1px solid Gray; width: ".(-$ld)."em;'></div></div>";}}foreach((array)$af[$A]as$Yf=>$cf){foreach($cf
as$kd=>$g){$ld=$kd-$Sf[$A][1];$q=0;foreach($g
as$Xf)echo"\n<div class='references' title='".h($Yf)."' id='refd$kd-".($q++)."' style='left: $ld"."em; top: ".$O["fields"][$Xf]["pos"]."em; height: 1.25em; background: url(".h(preg_replace("~\\?.*~","",ME))."?file=arrow.gif) no-repeat right center;&amp;version=3.7.0'><div style='height: .5em; border-bottom: 1px solid Gray; width: ".(-$ld)."em;'></div></div>";}}echo"\n</div>\n";}foreach($rf
as$A=>$O){foreach((array)$O["references"]as$Yf=>$cf){foreach($cf
as$kd=>$Xe){$Id=$lg;$zd=-10;foreach($Xe[0]as$w=>$Bf){$Fe=$O["pos"][0]+$O["fields"][$Bf]["pos"];$Ge=$rf[$Yf]["pos"][0]+$rf[$Yf]["fields"][$Xe[1][$w]]["pos"];$Id=min($Id,$Fe,$Ge);$zd=max($zd,$Fe,$Ge);}echo"<div class='references' id='refl$kd' style='left: $kd"."em; top: $Id"."em; padding: .5em 0;'><div style='border-right: 1px solid Gray; margin-top: 1px; height: ".($zd-$Id)."em;'></div></div>\n";}}}echo'</div>
<p><a href="',h(ME."schema=".urlencode($ea)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$l){$fb="";foreach(array("output","format","db_style","routines","events","table_style","auto_increment","triggers","data_style")as$w)$fb.="&$w=".urlencode($_POST[$w]);cookie("adminer_export",substr($fb,1));$Q=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$dc=dump_headers((count($Q)==1?key($Q):DB),(DB==""||count($Q)>1));$Wc=ereg('sql',$_POST["format"]);if($Wc)echo"-- Adminer $ia ".$Ab[DRIVER]." dump

".($v!="sql"?"":"SET NAMES utf8;
".($_POST["data_style"]?"SET foreign_key_checks = 0;
SET time_zone = ".q(substr(preg_replace('~^[^-]~','+\0',$h->result("SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP)")),0,6)).";
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
");$Jf=$_POST["db_style"];$j=array(DB);if(DB==""){$j=$_POST["databases"];if(is_string($j))$j=explode("\n",rtrim(str_replace("\r","",$j),"\n"));}foreach((array)$j
as$k){$b->dumpDatabase($k);if($h->select_db($k)){if($Wc&&ereg('CREATE',$Jf)&&($hb=$h->result("SHOW CREATE DATABASE ".idf_escape($k),1))){if($Jf=="DROP+CREATE")echo"DROP DATABASE IF EXISTS ".idf_escape($k).";\n";echo"$hb;\n";}if($Wc){if($Jf)echo
use_sql($k).";\n\n";$re="";if($_POST["routines"]){foreach(array("FUNCTION","PROCEDURE")as$lf){foreach(get_rows("SHOW $lf STATUS WHERE Db = ".q($k),null,"-- ")as$I)$re.=($Jf!='DROP+CREATE'?"DROP $lf IF EXISTS ".idf_escape($I["Name"]).";;\n":"").remove_definer($h->result("SHOW CREATE $lf ".idf_escape($I["Name"]),2)).";;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$I)$re.=($Jf!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($I["Name"]).";;\n":"").remove_definer($h->result("SHOW CREATE EVENT ".idf_escape($I["Name"]),3)).";;\n\n";}if($re)echo"DELIMITER ;;\n\n$re"."DELIMITER ;\n\n";}if($_POST["table_style"]||$_POST["data_style"]){$Y=array();foreach(table_status('',true)as$A=>$P){$O=(DB==""||in_array($A,(array)$_POST["tables"]));$mb=(DB==""||in_array($A,(array)$_POST["data"]));if($O||$mb){if($dc=="tar"){$jg=new
TmpFile;ob_start(array($jg,'write'),1e5);}$b->dumpTable($A,($O?$_POST["table_style"]:""),(is_view($P)?2:0));if(is_view($P))$Y[]=$A;elseif($mb){$n=fields($A);$b->dumpData($A,$_POST["data_style"],"SELECT *".convert_fields($n,$n)." FROM ".table($A));}if($Wc&&$_POST["triggers"]&&$O&&($ug=trigger_sql($A,$_POST["table_style"])))echo"\nDELIMITER ;;\n$ug\nDELIMITER ;\n";if($dc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$k/")."$A.csv",$jg);}elseif($Wc)echo"\n";}}foreach($Y
as$Ng)$b->dumpTable($Ng,$_POST["table_style"],1);if($dc=="tar")echo
pack("x512");}}}if($Wc)echo"-- ".$h->result("SELECT NOW()")."\n";exit;}page_header('Export',$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),DB);echo'
<form action="" method="post">
<table cellspacing="0">
';$pb=array('','USE','DROP+CREATE','CREATE');$Uf=array('','DROP+CREATE','CREATE');$nb=array('','TRUNCATE+INSERT','INSERT');if($v=="sql")$nb[]='INSERT+UPDATE';parse_str($_COOKIE["adminer_export"],$I);if(!$I)$I=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");if(!isset($I["events"])){$I["routines"]=$I["events"]=($_GET["dump"]=="");$I["triggers"]=$I["table_style"];}echo"<tr><th>".'Output'."<td>".html_select("output",$b->dumpOutput(),$I["output"],0)."\n";echo"<tr><th>".'Format'."<td>".html_select("format",$b->dumpFormat(),$I["format"],0)."\n";echo($v=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$pb,$I["db_style"]).(support("routine")?checkbox("routines",1,$I["routines"],'Routines'):"").(support("event")?checkbox("events",1,$I["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$Uf,$I["table_style"]).checkbox("auto_increment",1,$I["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$I["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$nb,$I["data_style"]),'</table>
<p><input type="submit" value="Export">
<input type="hidden" name="token" value="',$R,'">

<table cellspacing="0">
';$Je=array();if(DB!=""){$Na=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label><input type='checkbox' id='check-tables'$Na onclick='formCheck(this, /^tables\\[/);'>".'Tables'."</label>","<th style='text-align: right;'><label>".'Data'."<input type='checkbox' id='check-data'$Na onclick='formCheck(this, /^data\\[/);'></label>","</thead>\n";$Y="";$Vf=tables_list();foreach($Vf
as$A=>$S){$Ie=ereg_replace("_.*","",$A);$Na=($a==""||$a==(substr($a,-1)=="%"?"$Ie%":$A));$Me="<tr><td>".checkbox("tables[]",$A,$Na,$A,"checkboxClick(event, this); formUncheck('check-tables');");if($S!==null&&!eregi("table",$S))$Y.="$Me\n";else
echo"$Me<td align='right'><label><span id='Rows-".h($A)."'></span>".checkbox("data[]",$A,$Na,"","checkboxClick(event, this); formUncheck('check-data');")."</label>\n";$Je[$Ie]++;}echo$Y;if($Vf)echo"<script type='text/javascript'>ajaxSetHtml('".js_escape(ME)."script=db');</script>\n";}else{echo"<thead><tr><th style='text-align: left;'><label><input type='checkbox' id='check-databases'".($a==""?" checked":"")." onclick='formCheck(this, /^databases\\[/);'>".'Database'."</label></thead>\n";$j=$b->databases();if($j){foreach($j
as$k){if(!information_schema($k)){$Ie=ereg_replace("_.*","",$k);echo"<tr><td>".checkbox("databases[]",$k,$a==""||$a=="$Ie%",$k,"formUncheck('check-databases');")."</label>\n";$Je[$Ie]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$nc=true;foreach($Je
as$w=>$W){if($w!=""&&$W>1){echo($nc?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$w%")."'>".h($w)."</a>";$nc=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');$G=$h->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$_c=$G;if(!$G)$G=$h->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo"<input type='hidden' name='db' value='".h(DB)."'>\n",($_c?"":"<input type='hidden' name='grant' value=''>\n"),"<table cellspacing='0'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<th>&nbsp;</thead>\n";while($I=$G->fetch_assoc())echo'<tr'.odd().'><td>'.h($I["User"])."<td>".h($I["Host"]).'<td><a href="'.h(ME.'user='.urlencode($I["User"]).'&host='.urlencode($I["Host"])).'">'.'Edit'."</a>\n";if(!$_c||DB!="")echo"<tr".odd()."><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n",'<p><a href="'.h(ME).'user=">'.'Create user'."</a>";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){dump_headers("sql");$b->dumpTable("","");$b->dumpData("","table",$_POST["query"]);exit;}restart_session();$Hc=&get_session("queries");$Gc=&$Hc[DB];if(!$l&&$_POST["clear"]){$Gc=array();redirect(remove_from_uri("history"));}page_header('SQL command',$l);if(!$l&&$_POST){$vc=false;$F=$_POST["query"];if($_POST["webfile"]){$vc=@fopen((file_exists("adminer.sql")?"adminer.sql":"compress.zlib://adminer.sql.gz"),"rb");$F=($vc?fread($vc,1e6):false);}elseif($_FILES&&$_FILES["sql_file"]["error"][0]!=4)$F=get_file("sql_file",true);if(is_string($F)){if(function_exists('memory_get_usage'))@ini_set("memory_limit",max(ini_bytes("memory_limit"),2*strlen($F)+memory_get_usage()+8e6));if($F!=""&&strlen($F)<1e6){$E=$F.(ereg(";[ \t\r\n]*\$",$F)?"":";");if(!$Gc||reset(end($Gc))!=$E){restart_session();$Gc[]=array($E,time());set_session("queries",$Hc);stop_session();}}$Cf="(?:\\s|/\\*.*\\*/|(?:#|-- )[^\n]*\n|--\n)";$tb=";";$B=0;$Nb=true;$i=connect();if(is_object($i)&&DB!="")$i->select_db(DB);$Xa=0;$Tb=array();$qd=0;$we='[\'"'.($v=="sql"?'`#':($v=="sqlite"?'`[':($v=="mssql"?'[':''))).']|/\\*|-- |$'.($v=="pgsql"?'|\\$[^$]*\\$':'');$mg=microtime();parse_str($_COOKIE["adminer_export"],$pa);$Fb=$b->dumpFormat();unset($Fb["sql"]);while($F!=""){if(!$B&&preg_match("~^$Cf*DELIMITER\\s+(\\S+)~i",$F,$_)){$tb=$_[1];$F=substr($F,strlen($_[0]));}else{preg_match('('.preg_quote($tb)."\\s*|$we)",$F,$_,PREG_OFFSET_CAPTURE,$B);list($tc,$Ee)=$_[0];if(!$tc&&$vc&&!feof($vc))$F.=fread($vc,1e5);else{if(!$tc&&rtrim($F)=="")break;$B=$Ee+strlen($tc);if($tc&&rtrim($tc)!=$tb){while(preg_match('('.($tc=='/*'?'\\*/':($tc=='['?']':(ereg('^-- |^#',$tc)?"\n":preg_quote($tc)."|\\\\."))).'|$)s',$F,$_,PREG_OFFSET_CAPTURE,$B)){$pf=$_[0][0];if(!$pf&&$vc&&!feof($vc))$F.=fread($vc,1e5);else{$B=$_[0][1]+strlen($pf);if($pf[0]!="\\")break;}}}else{$Nb=false;$E=substr($F,0,$Ee);$Xa++;$Me="<pre id='sql-$Xa'><code class='jush-$v'>".shorten_utf8(trim($E),1000)."</code></pre>\n";if(!$_POST["only_errors"]){echo$Me;ob_flush();flush();}$Ef=microtime();if($h->multi_query($E)&&is_object($i)&&preg_match("~^$Cf*USE\\b~isU",$E))$i->query($E);do{$G=$h->store_result();$Ob=microtime();$dg=" <span class='time'>(".format_time($Ef,$Ob).")</span>".(strlen($E)<1000?" <a href='".h(ME)."sql=".urlencode(trim($E))."'>".'Edit'."</a>":"");if($h->error){echo($_POST["only_errors"]?$Me:""),"<p class='error'>".'Error in query'.($h->errno?" ($h->errno)":"").": ".error()."\n";$Tb[]=" <a href='#sql-$Xa'>$Xa</a>";if($_POST["error_stops"])break
2;}elseif(is_object($G)){$le=select($G,$i);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n","<p>".($G->num_rows?lang(array('%d row','%d rows'),$G->num_rows):"").$dg;$r="export-$Xa";$cc=", <a href='#$r' onclick=\"return !toggle('$r');\">".'Export'."</a><span id='$r' class='hidden'>: ".html_select("output",$b->dumpOutput(),$pa["output"])." ".html_select("format",$Fb,$pa["format"])."<input type='hidden' name='query' value='".h($E)."'>"." <input type='submit' name='export' value='".'Export'."'><input type='hidden' name='token' value='$R'></span>\n";if($i&&preg_match("~^($Cf|\\()*SELECT\\b~isU",$E)&&($bc=explain($i,$E))){$r="explain-$Xa";echo", <a href='#$r' onclick=\"return !toggle('$r');\">EXPLAIN</a>$cc","<div id='$r' class='hidden'>\n";select($bc,$i,($v=="sql"?"http://dev.mysql.com/doc/refman/".substr($h->server_info,0,3)."/en/explain-output.html#explain_":""),$le);echo"</div>\n";}else
echo$cc;echo"</form>\n";}}else{if(preg_match("~^$Cf*(CREATE|DROP|ALTER)$Cf+(DATABASE|SCHEMA)\\b~isU",$E)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h($h->info)."'>".lang(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$h->affected_rows)."$dg\n";}$Ef=$Ob;}while($h->next_result());$qd+=substr_count($E.$tc,"\n");$F=substr($F,$B);$B=0;}}}}if($Nb)echo"<p class='message'>".'No commands to execute.'."\n";elseif($_POST["only_errors"]){echo"<p class='message'>".lang(array('%d query executed OK.','%d queries executed OK.'),$Xa-count($Tb))," <span class='time'>(".format_time($mg,microtime()).")</span>\n";}elseif($Tb&&$Xa>1)echo"<p class='error'>".'Error in query'.": ".implode("",$Tb)."\n";}else
echo"<p class='error'>".upload_error($F)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
<p>';$E=$_GET["sql"];if($_POST)$E=$_POST["query"];elseif($_GET["history"]=="all")$E=$Gc;elseif($_GET["history"]!="")$E=$Gc[$_GET["history"]][0];textarea("query",$E,20);echo($_POST?"":"<script type='text/javascript'>focus(document.getElementsByTagName('textarea')[0]);</script>\n"),"<p>".(ini_bool("file_uploads")?'File upload'.': <input type="file" name="sql_file[]" multiple'.($_FILES&&$_FILES["sql_file"]["error"][0]!=4?'':' onchange="this.form[\'only_errors\'].checked = true;"').'> (&lt; '.ini_get("upload_max_filesize").'B)':'File uploads are disabled.'),'<p>
<input type="submit" value="Execute" title="Ctrl+Enter">
',checkbox("error_stops",1,$_POST["error_stops"],'Stop on error')."\n",checkbox("only_errors",1,$_POST["only_errors"],'Show only errors')."\n";print_fieldset("webfile",'From server',$_POST["webfile"],"document.getElementById('form')['only_errors'].checked = true; ");echo
sprintf('Webserver file %s',"<code>adminer.sql".(extension_loaded("zlib")?"[.gz]":"")."</code>"),' <input type="submit" name="webfile" value="'.'Run file'.'">',"</div></fieldset>\n";if($Gc){print_fieldset("history",'History',$_GET["history"]!="");for($W=end($Gc);$W;$W=prev($Gc)){$w=key($Gc);list($E,$dg)=$W;echo'<a href="'.h(ME."sql=&history=$w").'">'.'Edit'."</a> <span class='time' title='".@date('Y-m-d',$dg)."'>".@date("H:i:s",$dg)."</span> <code class='jush-$v'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace('~^(#|-- ).*~m','',$E)))),80,"</code>")."<br>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?(count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$Dg=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$A=>$m){if(!isset($m["privileges"][$Dg?"update":"insert"])||$b->fieldName($m)=="")unset($n[$A]);}if($_POST&&!$l&&!isset($_GET["select"])){$z=$_POST["referer"];if($_POST["insert"])$z=($Dg?null:$_SERVER["REQUEST_URI"]);elseif(!ereg('^.+&select=.+$',$z))$z=ME."select=".urlencode($a);$u=indexes($a);$zg=unique_array($_GET["where"],$u);$Ue="\nWHERE $Z";if(isset($_POST["delete"])){$F="FROM ".table($a);query_redirect("DELETE".($zg?" $F$Ue":limit1($F,$Ue)),$z,'Item has been deleted.');}else{$M=array();foreach($n
as$A=>$m){$W=process_input($m);if($W!==false&&$W!==null)$M[idf_escape($A)]=($Dg?"\n".idf_escape($A)." = $W":$W);}if($Dg){if(!$M)redirect($z);$F=table($a)." SET".implode(",",$M);query_redirect("UPDATE".($zg?" $F$Ue":limit1($F,$Ue)),$z,'Item has been updated.');}else{$G=insert_into($a,$M);$jd=($G?last_id():0);queries_redirect($z,sprintf('Item%s has been inserted.',($jd?" $jd":"")),$G);}}}$Qf=$b->tableName(table_status($a,true));page_header(($Dg?'Edit':'Insert'),$l,array("select"=>array($a,$Qf)),$Qf);$I=null;if($_POST["save"])$I=(array)$_POST["fields"];elseif($Z){$K=array();foreach($n
as$A=>$m){if(isset($m["privileges"]["select"])){$wa=convert_field($m);if($_POST["clone"]&&$m["auto_increment"])$wa="''";if($v=="sql"&&ereg("enum|set",$m["type"]))$wa="1*".idf_escape($A);$K[]=($wa?"$wa AS ":"").idf_escape($A);}}$I=array();if($K){$J=get_rows("SELECT".limit(implode(", ",$K)." FROM ".table($a)," WHERE $Z",(isset($_GET["select"])?2:1)));$I=(isset($_GET["select"])&&count($J)!=1?null:reset($J));}}if($I===false)echo"<p class='error'>".'No rows.'."\n";echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';if(!$n)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table cellspacing='0' onkeydown='return editingKeydown(event);'>\n";foreach($n
as$A=>$m){echo"<tr><th>".$b->fieldName($m);$sb=$_GET["set"][bracket_escape($A)];if($sb===null){$sb=$m["default"];if($m["type"]=="bit"&&ereg("^b'([01]*)'\$",$sb,$df))$sb=$df[1];}$X=($I!==null?($I[$A]!=""&&$v=="sql"&&ereg("enum|set",$m["type"])?(is_array($I[$A])?array_sum($I[$A]):+$I[$A]):$I[$A]):(!$Dg&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$sb)));if(!$_POST["save"]&&is_string($X))$X=$b->editVal($X,$m);$p=($_POST["save"]?(string)$_POST["function"][$A]:($Dg&&$m["on_update"]=="CURRENT_TIMESTAMP"?"now":($X===false?null:($X!==null?'':'NULL'))));if(ereg("time",$m["type"])&&$X=="CURRENT_TIMESTAMP"){$X="";$p="now";}input($m,$X,$p);echo"\n";}echo"</table>\n";}echo'<p>
';if($n){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Dg?'Save and continue edit':'Save and insert next')."' title='Ctrl+Shift+Enter'>\n";}echo($Dg?"<input type='submit' name='delete' value='".'Delete'."' onclick=\"return confirm('".'Are you sure?'."');\">\n":($_POST||!$n?"":"<script type='text/javascript'>focus(document.getElementById('form').getElementsByTagName('td')[1].firstChild);</script>\n"));if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo'<input type="hidden" name="referer" value="',h(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"]),'">
<input type="hidden" name="save" value="1">
<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["create"])){$a=$_GET["create"];$xe=array('HASH','LINEAR HASH','KEY','LINEAR KEY','RANGE','LIST');$Ze=referencable_primary($a);$rc=array();foreach($Ze
as$Qf=>$m)$rc[str_replace("`","``",$Qf)."`".str_replace("`","``",$m["field"])]=$Qf;$oe=array();$pe=array();if($a!=""){$oe=fields($a);$pe=table_status($a);}$I=$_POST;$I["fields"]=(array)$I["fields"];if($I["auto_increment_col"])$I["fields"][$I["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!process_fields($I["fields"])&&!$l){if($_POST["drop"])query_redirect("DROP TABLE ".table($a),substr(ME,0,-1),'Table has been dropped.');else{$n=array();$va=array();$Fg=false;$pc=array();ksort($I["fields"]);$ne=reset($oe);$ta=" FIRST";foreach($I["fields"]as$w=>$m){$o=$rc[$m["type"]];$vg=($o!==null?$Ze[$o]:$m);if($m["field"]!=""){if(!$m["has_default"])$m["default"]=null;if($w==$I["auto_increment_col"])$m["auto_increment"]=true;$Re=process_field($m,$vg);$va[]=array($m["orig"],$Re,$ta);if($Re!=process_field($ne,$ne)){$n[]=array($m["orig"],$Re,$ta);if($m["orig"]!=""||$ta)$Fg=true;}if($o!==null)$pc[idf_escape($m["field"])]=($a!=""&&$v!="sqlite"?"ADD":" ")." FOREIGN KEY (".idf_escape($m["field"]).") REFERENCES ".table($rc[$m["type"]])." (".idf_escape($vg["field"]).")".(ereg("^($ae)\$",$m["on_delete"])?" ON DELETE $m[on_delete]":"");$ta=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$Fg=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$ne=next($oe);if(!$ne)$ta="";}}$ze="";if(in_array($I["partition_by"],$xe)){$_e=array();if($I["partition_by"]=='RANGE'||$I["partition_by"]=='LIST'){foreach(array_filter($I["partition_names"])as$w=>$W){$X=$I["partition_values"][$w];$_e[]="\n  PARTITION ".idf_escape($W)." VALUES ".($I["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$ze.="\nPARTITION BY $I[partition_by]($I[partition])".($_e?" (".implode(",",$_e)."\n)":($I["partitions"]?" PARTITIONS ".(+$I["partitions"]):""));}elseif(support("partitioning")&&ereg("partitioned",$pe["Create_options"]))$ze.="\nREMOVE PARTITIONING";$Cd='Table has been altered.';if($a==""){cookie("adminer_engine",$I["Engine"]);$Cd='Table has been created.';}$A=trim($I["name"]);queries_redirect(ME."table=".urlencode($A),$Cd,alter_table($a,$A,($v=="sqlite"&&($Fg||$pc)?$va:$n),$pc,$I["Comment"],($I["Engine"]&&$I["Engine"]!=$pe["Engine"]?$I["Engine"]:""),($I["Collation"]&&$I["Collation"]!=$pe["Collation"]?$I["Collation"]:""),($I["Auto_increment"]!=""?+$I["Auto_increment"]:""),$ze));}}page_header(($a!=""?'Alter table':'Create table'),$l,array("table"=>$a),$a);if(!$_POST){$I=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($T["int"])?"int":(isset($T["integer"])?"integer":"")))),"partition_names"=>array(""),);if($a!=""){$I=$pe;$I["name"]=$a;$I["fields"]=array();if(!$_GET["auto_increment"])$I["Auto_increment"]="";foreach($oe
as$m){$m["has_default"]=isset($m["default"]);$I["fields"][]=$m;}if(support("partitioning")){$wc="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($a);$G=$h->query("SELECT PARTITION_METHOD, PARTITION_ORDINAL_POSITION, PARTITION_EXPRESSION $wc ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");list($I["partition_by"],$I["partitions"],$I["partition"])=$G->fetch_row();$_e=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $wc AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$_e[""]="";$I["partition_names"]=array_keys($_e);$I["partition_values"]=array_values($_e);}}}$Ua=collations();$Qb=engines();foreach($Qb
as$Pb){if(!strcasecmp($Pb,$I["Engine"])){$I["Engine"]=$Pb;break;}}echo'
<form action="" method="post" id="form">
<p>
Table name: <input name="name" maxlength="64" value="',h($I["name"]),'" autocapitalize="off">
';if($a==""&&!$_POST){?><script type='text/javascript'>focus(document.getElementById('form')['name']);</script><?php }echo($Qb?html_select("Engine",array(""=>"(".'engine'.")")+$Qb,$I["Engine"]):""),' ',($Ua&&!ereg("sqlite|mssql",$v)?html_select("Collation",array(""=>"(".'collation'.")")+$Ua,$I["Collation"]):""),' <input type="submit" value="Save">
<table cellspacing="0" id="edit-fields" class="nowrap">
';$Za=($_POST?$_POST["comments"]:$I["Comment"]!="");if(!$_POST&&!$Za){foreach($I["fields"]as$m){if($m["comment"]!=""){$Za=true;break;}}}edit_fields($I["fields"],$Ua,"TABLE",$rc,$Za);echo'</table>
<p>
Auto Increment: <input type="number" name="Auto_increment" size="6" value="',h($I["Auto_increment"]),'">
<label class="jsonly"><input type="checkbox" id="defaults" name="defaults" value="1" checked onclick="columnShow(this.checked, 5);">Default values</label>
';if(!$_POST["defaults"]){echo'<script type="text/javascript">editingHideDefaults()</script>';}echo(support("comment")?checkbox("comments",1,$Za,'Comment',"columnShow(this.checked, 6); toggle('Comment'); if (this.checked) this.form['Comment'].focus();",true).' <input name="Comment" id="Comment" value="'.h($I["Comment"]).'" maxlength="'.($h->server_info>=5.5?2048:60).'"'.($Za?'':' class="hidden"').'>':''),'<p>
<input type="submit" value="Save">
';if($_GET["create"]!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}if(support("partitioning")){$ye=ereg('RANGE|LIST',$I["partition_by"]);print_fieldset("partition",'Partition by',$I["partition_by"]);echo'<p>
',html_select("partition_by",array(-1=>"")+$xe,$I["partition_by"],"partitionByChange(this);"),'(<input name="partition" value="',h($I["partition"]),'">)
Partitions: <input type="number" name="partitions" class="size" value="',h($I["partitions"]),'"',($ye||!$I["partition_by"]?" class='hidden'":""),'>
<table cellspacing="0" id="partition-table"',($ye?"":" class='hidden'"),'>
<thead><tr><th>Partition name<th>Values</thead>
';foreach($I["partition_names"]as$w=>$W){echo'<tr>','<td><input name="partition_names[]" value="'.h($W).'"'.($w==count($I["partition_names"])-1?' onchange="partitionNameChange(this);"':'').' autocapitalize="off">','<td><input name="partition_values[]" value="'.h($I["partition_values"][$w]).'">';}echo'</table>
</div></fieldset>
';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Nc=array("PRIMARY","UNIQUE","INDEX");$P=table_status($a,true);if(eregi("MyISAM|M?aria".($h->server_info>=5.6?"|InnoDB":""),$P["Engine"]))$Nc[]="FULLTEXT";$u=indexes($a);if($v=="sqlite"){unset($Nc[0]);unset($u[""]);}$I=$_POST;if($_POST&&!$l&&!$_POST["add"]){$c=array();foreach($I["indexes"]as$t){$A=$t["name"];if(in_array($t["type"],$Nc)){$g=array();$pd=array();$M=array();ksort($t["columns"]);foreach($t["columns"]as$w=>$f){if($f!=""){$od=$t["lengths"][$w];$M[]=idf_escape($f).($od?"(".(+$od).")":"");$g[]=$f;$pd[]=($od?$od:null);}}if($g){$ac=$u[$A];if($ac){ksort($ac["columns"]);ksort($ac["lengths"]);if($t["type"]==$ac["type"]&&array_values($ac["columns"])===$g&&(!$ac["lengths"]||array_values($ac["lengths"])===$pd)){unset($u[$A]);continue;}}$c[]=array($t["type"],$A,"(".implode(", ",$M).")");}}}foreach($u
as$A=>$ac)$c[]=array($ac["type"],$A,"DROP");if(!$c)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),'Indexes have been altered.',alter_indexes($a,$c));}page_header('Indexes',$l,array("table"=>$a),$a);$n=array_keys(fields($a));if($_POST["add"]){foreach($I["indexes"]as$w=>$t){if($t["columns"][count($t["columns"])]!="")$I["indexes"][$w]["columns"][]="";}$t=end($I["indexes"]);if($t["type"]||array_filter($t["columns"],'strlen')||array_filter($t["lengths"],'strlen'))$I["indexes"][]=array("columns"=>array(1=>""));}if(!$I){foreach($u
as$w=>$t){$u[$w]["name"]=$w;$u[$w]["columns"][]="";}$u[]=array("columns"=>array(1=>""));$I["indexes"]=$u;}echo'
<form action="" method="post">
<table cellspacing="0" class="nowrap">
<thead><tr><th>Index Type<th>Column (length)<th>Name</thead>
';$Zc=1;foreach($I["indexes"]as$t){echo"<tr><td>".html_select("indexes[$Zc][type]",array(-1=>"")+$Nc,$t["type"],($Zc==count($I["indexes"])?"indexesAddRow(this);":1))."<td>";ksort($t["columns"]);$q=1;foreach($t["columns"]as$w=>$f){echo"<span>".html_select("indexes[$Zc][columns][$q]",array(-1=>"")+$n,$f,($q==count($t["columns"])?"indexesAddColumn":"indexesChangeColumn")."(this, '".js_escape($v=="sql"?"":$_GET["indexes"]."_")."');"),"<input type='number' name='indexes[$Zc][lengths][$q]' class='size' value='".h($t["lengths"][$w])."'> </span>";$q++;}echo"<td><input name='indexes[$Zc][name]' value='".h($t["name"])."' autocapitalize='off'>\n";$Zc++;}echo'</table>
<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add next"></noscript>
<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["database"])){$I=$_POST;if($_POST&&!$l&&!isset($_POST["add_x"])){restart_session();$A=trim($I["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif(DB!==$A){if(DB!=""){$_GET["db"]=$A;queries_redirect(preg_replace('~db=[^&]*&~','',ME)."db=".urlencode($A),'Database has been renamed.',rename_database($A,$I["collation"]));}else{$j=explode("\n",str_replace("\r","",$A));$Kf=true;$id="";foreach($j
as$k){if(count($j)==1||$k!=""){if(!create_database($k,$I["collation"]))$Kf=false;$id=$k;}}queries_redirect(ME."db=".urlencode($id),'Database has been created.',$Kf);}}else{if(!$I["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($A).(eregi('^[a-z0-9_]+$',$I["collation"])?" COLLATE $I[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$l,array(),DB);$Ua=collations();$A=DB;if($_POST)$A=$I["name"];elseif(DB!="")$I["collation"]=db_collation(DB,$Ua);elseif($v=="sql"){foreach(get_vals("SHOW GRANTS")as$_c){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\\.\\*)?~',$_c,$_)&&$_[1]){$A=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add_x"]||strpos($A,"\n")?'<textarea id="name" name="name" rows="10" cols="40">'.h($A).'</textarea><br>':'<input name="name" id="name" value="'.h($A).'" maxlength="64" autocapitalize="off">')."\n".($Ua?html_select("collation",array(""=>"(".'collation'.")")+$Ua,$I["collation"]):"");?>
<script type='text/javascript'>focus(document.getElementById('name'));</script>
<input type="submit" value="Save">
<?php
if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n";elseif(!$_POST["add_x"]&&$_GET["db"]=="")echo"<input type='image' name='add' src='".h(preg_replace("~\\?.*~","",ME))."?file=plus.gif&amp;version=3.7.0' alt='+' title='".'Add next'."'>\n";echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["scheme"])){$I=$_POST;if($_POST&&!$l){$y=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$y,'Schema has been dropped.');else{$A=trim($I["name"]);$y.=urlencode($A);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($A),$y,'Schema has been created.');elseif($_GET["ns"]!=$A)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($A),$y,'Schema has been altered.');else
redirect($y);}}page_header($_GET["ns"]!=""?'Alter schema':'Create schema',$l);if(!$I)$I["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" id="name" value="',h($I["name"]);?>" autocapitalize="off">
<script type='text/javascript'>focus(document.getElementById('name'));</script>
<input type="submit" value="Save">
<?php
if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n";echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["call"])){$da=$_GET["call"];page_header('Call'.": ".h($da),$l);$lf=routine($da,(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Mc=array();$re=array();foreach($lf["fields"]as$q=>$m){if(substr($m["inout"],-3)=="OUT")$re[$q]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Mc[]=$q;}if(!$l&&$_POST){$Ka=array();foreach($lf["fields"]as$w=>$m){if(in_array($w,$Mc)){$W=process_input($m);if($W===false)$W="''";if(isset($re[$w]))$h->query("SET @".idf_escape($m["field"])." = $W");}$Ka[]=(isset($re[$w])?"@".idf_escape($m["field"]):$W);}$F=(isset($_GET["callf"])?"SELECT":"CALL")." ".idf_escape($da)."(".implode(", ",$Ka).")";echo"<p><code class='jush-$v'>".h($F)."</code> <a href='".h(ME)."sql=".urlencode($F)."'>".'Edit'."</a>\n";if(!$h->multi_query($F))echo"<p class='error'>".error()."\n";else{$i=connect();if(is_object($i))$i->select_db(DB);do{$G=$h->store_result();if(is_object($G))select($G,$i);else
echo"<p class='message'>".lang(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$h->affected_rows)."\n";}while($h->next_result());if($re)select($h->query("SELECT ".implode(", ",$re)));}}echo'
<form action="" method="post">
';if($Mc){echo"<table cellspacing='0'>\n";foreach($Mc
as$w){$m=$lf["fields"][$w];$A=$m["field"];echo"<tr><th>".$b->fieldName($m);$X=$_POST["fields"][$A];if($X!=""){if($m["type"]=="enum")$X=+$X;if($m["type"]=="set")$X=array_sum($X);}input($m,$X,(string)$_POST["function"][$A]);echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="Call">
<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$A=$_GET["name"];$I=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if($_POST["drop"])query_redirect("ALTER TABLE ".table($a)."\nDROP ".($v=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A),ME."table=".urlencode($a),'Foreign key has been dropped.');else{$Bf=array_filter($I["source"],'strlen');ksort($Bf);$Xf=array();foreach($Bf
as$w=>$W)$Xf[$w]=$I["target"][$w];query_redirect("ALTER TABLE ".table($a).($A!=""?"\nDROP ".($v=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A).",":"")."\nADD FOREIGN KEY (".implode(", ",array_map('idf_escape',$Bf)).") REFERENCES ".table($I["table"])." (".implode(", ",array_map('idf_escape',$Xf)).")".(ereg("^($ae)\$",$I["on_delete"])?" ON DELETE $I[on_delete]":"").(ereg("^($ae)\$",$I["on_update"])?" ON UPDATE $I[on_update]":""),ME."table=".urlencode($a),($A!=""?'Foreign key has been altered.':'Foreign key has been created.'));$l='Source and target columns must have the same data type, there must be an index on the target columns and referenced data must exist.'."<br>$l";}}page_header('Foreign key',$l,array("table"=>$a),$a);if($_POST){ksort($I["source"]);if($_POST["add"])$I["source"][]="";elseif($_POST["change"]||$_POST["change-js"])$I["target"]=array();}elseif($A!=""){$rc=foreign_keys($a);$I=$rc[$A];$I["source"][]="";}else{$I["table"]=$a;$I["source"]=array("");}$Bf=array_keys(fields($a));$Xf=($a===$I["table"]?$Bf:array_keys(fields($I["table"])));$Ye=array_keys(array_filter(table_status('',true),'fk_support'));echo'
<form action="" method="post">
<p>
';if($I["db"]==""&&$I["ns"]==""){echo'Target table:
',html_select("table",$Ye,$I["table"],"this.form['change-js'].value = '1'; this.form.submit();"),'<input type="hidden" name="change-js" value="">
<noscript><p><input type="submit" name="change" value="Change"></noscript>
<table cellspacing="0">
<thead><tr><th>Source<th>Target</thead>
';$Zc=0;foreach($I["source"]as$w=>$W){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$Bf,$W,($Zc==count($I["source"])-1?"foreignAddRow(this);":1)),"<td>".html_select("target[".(+$w)."]",$Xf,$I["target"][$w]);$Zc++;}echo'</table>
<p>
ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",$ae),$I["on_delete"]),' ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",$ae),$I["on_update"]),'<p>
<input type="submit" value="Save">
<noscript><p><input type="submit" name="add" value="Add column"></noscript>
';}if($A!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$I=$_POST;if($_POST&&!$l){$A=trim($I["name"]);$wa=" AS\n$I[select]";$z=ME."table=".urlencode($A);$Cd='View has been altered.';if(!$_POST["drop"]&&$a==$A&&$v!="sqlite")query_redirect(($v=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($A).$wa,$z,$Cd);else{$Zf=$A."_adminer_".uniqid();drop_create("DROP VIEW ".table($a),"CREATE VIEW ".table($A).$wa,"DROP VIEW ".table($A),"CREATE VIEW ".table($Zf).$wa,"DROP VIEW ".table($Zf),($_POST["drop"]?substr(ME,0,-1):$z),'View has been dropped.',$Cd,'View has been created.',$a,$A);}}page_header(($a!=""?'Alter view':'Create view'),$l,array("table"=>$a),$a);if(!$_POST&&$a!=""){$I=view($a);$I["name"]=$a;}echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($I["name"]),'" maxlength="64" autocapitalize="off">
<p>';textarea("select",$I["select"]);echo'<p>
<input type="submit" value="Save">
';if($_GET["view"]!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Uc=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Gf=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$I=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($I["INTERVAL_FIELD"],$Uc)&&isset($Gf[$I["STATUS"]])){$qf="\nON SCHEDULE ".($I["INTERVAL_VALUE"]?"EVERY ".q($I["INTERVAL_VALUE"])." $I[INTERVAL_FIELD]".($I["STARTS"]?" STARTS ".q($I["STARTS"]):"").($I["ENDS"]?" ENDS ".q($I["ENDS"]):""):"AT ".q($I["STARTS"]))." ON COMPLETION".($I["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$qf.($aa!=$I["EVENT_NAME"]?"\nRENAME TO ".idf_escape($I["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($I["EVENT_NAME"]).$qf)."\n".$Gf[$I["STATUS"]]." COMMENT ".q($I["EVENT_COMMENT"]).rtrim(" DO\n$I[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$l);if(!$I&&$aa!=""){$J=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$I=reset($J);}echo'
<form action="" method="post">
<table cellspacing="0">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($I["EVENT_NAME"]),'" maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$I[EXECUTE_AT]$I[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($I["ENDS"]),'">
<tr><th>Every<td><input type="number" name="INTERVAL_VALUE" value="',h($I["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Uc,$I["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$Gf,$I["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($I["EVENT_COMMENT"]),'" maxlength="64">
<tr><th>&nbsp;<td>',checkbox("ON_COMPLETION","PRESERVE",$I["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$I["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="Save">
';if($aa!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["procedure"])){$da=$_GET["procedure"];$lf=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$I=$_POST;$I["fields"]=(array)$I["fields"];if($_POST&&!process_fields($I["fields"])&&!$l){$Zf="$I[name]_adminer_".uniqid();drop_create("DROP $lf ".idf_escape($da),create_routine($lf,$I),"DROP $lf ".idf_escape($I["name"]),create_routine($lf,array("name"=>$Zf)+$I),"DROP $lf ".idf_escape($Zf),substr(ME,0,-1),'Routine has been dropped.','Routine has been altered.','Routine has been created.',$da,$I["name"]);}page_header(($da!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($da):(isset($_GET["function"])?'Create function':'Create procedure')),$l);if(!$_POST&&$da!=""){$I=routine($da,$lf);$I["name"]=$da;}$Ua=get_vals("SHOW CHARACTER SET");sort($Ua);$mf=routine_languages();echo'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($I["name"]),'" maxlength="64" autocapitalize="off">
',($mf?'Language'.": ".html_select("language",$mf,$I["language"]):""),'<table cellspacing="0" class="nowrap">
';edit_fields($I["fields"],$Ua,$lf);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",$I["returns"],$Ua);}echo'</table>
<p>';textarea("definition",$I["definition"]);echo'<p>
<input type="submit" value="Save">
';if($da!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["sequence"])){$fa=$_GET["sequence"];$I=$_POST;if($_POST&&!$l){$y=substr(ME,0,-1);$A=trim($I["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($fa),$y,'Sequence has been dropped.');elseif($fa=="")query_redirect("CREATE SEQUENCE ".idf_escape($A),$y,'Sequence has been created.');elseif($fa!=$A)query_redirect("ALTER SEQUENCE ".idf_escape($fa)." RENAME TO ".idf_escape($A),$y,'Sequence has been altered.');else
redirect($y);}page_header($fa!=""?'Alter sequence'.": ".h($fa):'Create sequence',$l);if(!$I)$I["name"]=$fa;echo'
<form action="" method="post">
<p><input name="name" value="',h($I["name"]),'" autocapitalize="off">
<input type="submit" value="Save">
';if($fa!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n";echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["type"])){$ga=$_GET["type"];$I=$_POST;if($_POST&&!$l){$y=substr(ME,0,-1);if($_POST["drop"])query_redirect("DROP TYPE ".idf_escape($ga),$y,'Type has been dropped.');else
query_redirect("CREATE TYPE ".idf_escape(trim($I["name"]))." $I[as]",$y,'Type has been created.');}page_header($ga!=""?'Alter type'.": ".h($ga):'Create type',$l);if(!$I)$I["as"]="AS ";echo'
<form action="" method="post">
<p>
';if($ga!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n";else{echo"<input name='name' value='".h($I['name'])."' autocapitalize='off'>\n";textarea("as",$I["as"]);echo"<p><input type='submit' value='".'Save'."'>\n";}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$A=$_GET["name"];$tg=trigger_options();$rg=array("INSERT","UPDATE","DELETE");$I=(array)trigger($A)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$tg["Timing"])&&in_array($_POST["Event"],$rg)&&in_array($_POST["Type"],$tg["Type"])){$Zd=" ON ".table($a);$Bb="DROP TRIGGER ".idf_escape($A).($v=="pgsql"?$Zd:"");$z=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($Bb,$z,'Trigger has been dropped.');else{if($A!="")queries($Bb);queries_redirect($z,($A!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($Zd,$_POST)));if($A!="")queries(create_trigger($Zd,$I+array("Type"=>reset($tg["Type"]))));}}$I=$_POST;}page_header(($A!=""?'Alter trigger'.": ".h($A):'Create trigger'),$l,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table cellspacing="0">
<tr><th>Time<td>',html_select("Timing",$tg["Timing"],$I["Timing"],"if (/^".preg_quote($a,"/")."_[ba][iud]$/.test(this.form['Trigger'].value)) this.form['Trigger'].value = '".js_escape($a)."_' + selectValue(this).charAt(0).toLowerCase() + selectValue(this.form['Event']).charAt(0).toLowerCase();"),'<tr><th>Event<td>',html_select("Event",$rg,$I["Event"],"this.form['Timing'].onchange();"),'<tr><th>Type<td>',html_select("Type",$tg["Type"],$I["Type"]),'</table>
<p>Name: <input name="Trigger" value="',h($I["Trigger"]),'" maxlength="64" autocapitalize="off">
<p>';textarea("Statement",$I["Statement"]);echo'<p>
<input type="submit" value="Save">
';if($A!=""){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["user"])){$ha=$_GET["user"];$Pe=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$I){foreach(explode(",",($I["Privilege"]=="Grant option"?"":$I["Context"]))as$eb)$Pe[$eb][$I["Privilege"]]=$I["Comment"];}$Pe["Server Admin"]+=$Pe["File access on server"];$Pe["Databases"]["Create routine"]=$Pe["Procedures"]["Create routine"];unset($Pe["Procedures"]["Create routine"]);$Pe["Columns"]=array();foreach(array("Select","Insert","Update","References")as$W)$Pe["Columns"][$W]=$Pe["Tables"][$W];unset($Pe["Server Admin"]["Usage"]);foreach($Pe["Tables"]as$w=>$W)unset($Pe["Databases"][$w]);$Od=array();if($_POST){foreach($_POST["objects"]as$w=>$W)$Od[$W]=(array)$Od[$W]+(array)$_POST["grants"][$w];}$Ac=array();$Xd="";if(isset($_GET["host"])&&($G=$h->query("SHOW GRANTS FOR ".q($ha)."@".q($_GET["host"])))){while($I=$G->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$I[0],$_)&&preg_match_all('~ *([^(,]*[^ ,(])( *\\([^)]+\\))?~',$_[1],$vd,PREG_SET_ORDER)){foreach($vd
as$W){if($W[1]!="USAGE")$Ac["$_[2]$W[2]"][$W[1]]=true;if(ereg(' WITH GRANT OPTION',$I[0]))$Ac["$_[2]$W[2]"]["GRANT OPTION"]=true;}}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$I[0],$_))$Xd=$_[1];}}if($_POST&&!$l){$Yd=(isset($_GET["host"])?q($ha)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Yd",ME."privileges=",'User has been dropped.');else{$Qd=q($_POST["user"])."@".q($_POST["host"]);$Ae=$_POST["pass"];if($Ae!=''&&!$_POST["hashed"]){$Ae=$h->result("SELECT PASSWORD(".q($Ae).")");$l=!$Ae;}$jb=false;if(!$l){if($Yd!=$Qd){$jb=queries(($h->server_info<5?"GRANT USAGE ON *.* TO":"CREATE USER")." $Qd IDENTIFIED BY PASSWORD ".q($Ae));$l=!$jb;}elseif($Ae!=$Xd)queries("SET PASSWORD FOR $Qd = ".q($Ae));}if(!$l){$if=array();foreach($Od
as$Td=>$_c){if(isset($_GET["grant"]))$_c=array_filter($_c);$_c=array_keys($_c);if(isset($_GET["grant"]))$if=array_diff(array_keys(array_filter($Od[$Td],'strlen')),$_c);elseif($Yd==$Qd){$Vd=array_keys((array)$Ac[$Td]);$if=array_diff($Vd,$_c);$_c=array_diff($_c,$Vd);unset($Ac[$Td]);}if(preg_match('~^(.+)\\s*(\\(.*\\))?$~U',$Td,$_)&&(!grant("REVOKE",$if,$_[2]," ON $_[1] FROM $Qd")||!grant("GRANT",$_c,$_[2]," ON $_[1] TO $Qd"))){$l=true;break;}}}if(!$l&&isset($_GET["host"])){if($Yd!=$Qd)queries("DROP USER $Yd");elseif(!isset($_GET["grant"])){foreach($Ac
as$Td=>$if){if(preg_match('~^(.+)(\\(.*\\))?$~U',$Td,$_))grant("REVOKE",array_keys($if),$_[2]," ON $_[1] FROM $Qd");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),!$l);if($jb)$h->query("DROP USER $Qd");}}page_header((isset($_GET["host"])?'Username'.": ".h("$ha@$_GET[host]"):'Create user'),$l,array("privileges"=>array('','Privileges')));if($_POST){$I=$_POST;$Ac=$Od;}else{$I=$_GET+array("host"=>$h->result("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$I["pass"]=$Xd;if($Xd!="")$I["hashed"]=true;$Ac[(DB==""||$Ac?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table cellspacing="0">
<tr><th>Server<td><input name="host" maxlength="60" value="',h($I["host"]),'" autocapitalize="off">
<tr><th>Username<td><input name="user" maxlength="16" value="',h($I["user"]),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($I["pass"]),'">
';if(!$I["hashed"]){echo'<script type="text/javascript">typePassword(document.getElementById(\'pass\'));</script>';}echo
checkbox("hashed",1,$I["hashed"],'Hashed',"typePassword(this.form['pass'], this.checked);"),'</table>

';echo"<table cellspacing='0'>\n","<thead><tr><th colspan='2'><a href='http://dev.mysql.com/doc/refman/".substr($h->server_info,0,3)."/en/grant.html#priv_level' target='_blank' rel='noreferrer' class='help'>".'Privileges'."</a>";$q=0;foreach($Ac
as$Td=>$_c){echo'<th>'.($Td!="*.*"?"<input name='objects[$q]' value='".h($Td)."' size='10' autocapitalize='off'>":"<input type='hidden' name='objects[$q]' value='*.*' size='10'>*.*");$q++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Columns"=>'Column',"Procedures"=>'Routine',)as$eb=>$ub){foreach((array)$Pe[$eb]as$Oe=>$Ya){echo"<tr".odd()."><td".($ub?">$ub<td":" colspan='2'").' lang="en" title="'.h($Ya).'">'.h($Oe);$q=0;foreach($Ac
as$Td=>$_c){$A="'grants[$q][".h(strtoupper($Oe))."]'";$X=$_c[strtoupper($Oe)];if($eb=="Server Admin"&&$Td!=(isset($Ac["*.*"])?"*.*":".*"))echo"<td>&nbsp;";elseif(isset($_GET["grant"]))echo"<td><select name=$A><option><option value='1'".($X?" selected":"").">".'Grant'."<option value='0'".($X=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><input type='checkbox' name=$A value='1'".($X?" checked":"").($Oe=="All privileges"?" id='grants-$q-all'":($Oe=="Grant option"?"":" onclick=\"if (this.checked) formUncheck('grants-$q-all');\"")).">";$q++;}}}echo"</table>\n",'<p>
<input type="submit" value="Save">
';if(isset($_GET["host"])){echo'<input type="submit" name="drop" value="Drop"',confirm(),'>';}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")&&$_POST&&!$l){$fd=0;foreach((array)$_POST["kill"]as$W){if(queries("KILL ".(+$W)))$fd++;}queries_redirect(ME."processlist=",lang(array('%d process has been killed.','%d processes have been killed.'),$fd),$fd||!$_POST["kill"]);}page_header('Process list',$l);echo'
<form action="" method="post">
<table cellspacing="0" onclick="tableClick(event);" ondblclick="tableClick(event, true);" class="nowrap checkable">
';$q=-1;foreach(process_list()as$q=>$I){if(!$q){echo"<thead><tr lang='en'>".(support("kill")?"<th>&nbsp;":"");foreach($I
as$w=>$W)echo"<th>".($v=="sql"?"<a href='http://dev.mysql.com/doc/refman/".substr($h->server_info,0,3)."/en/show-processlist.html#processlist_".strtolower($w)."' target='_blank' rel='noreferrer' class='help'>$w</a>":$w);echo"</thead>\n";}echo"<tr".odd().">".(support("kill")?"<td>".checkbox("kill[]",$I["Id"],0):"");foreach($I
as$w=>$W)echo"<td>".(($v=="sql"&&$w=="Info"&&ereg("Query|Killed",$I["Command"])&&$W!="")||($v=="pgsql"&&$w=="current_query"&&$W!="<IDLE>")||($v=="oracle"&&$w=="sql_text"&&$W!="")?"<code class='jush-$v'>".shorten_utf8($W,100,"</code>").' <a href="'.h(ME.($I["db"]!=""?"db=".urlencode($I["db"])."&":"")."sql=".urlencode($W)).'">'.'Clone'.'</a>':nbsp($W));echo"\n";}echo'</table>
<script type=\'text/javascript\'>tableCheck();</script>
<p>
';if(support("kill")){echo($q+1)."/".sprintf('%d in total',$h->result("SELECT @@max_connections")),"<p><input type='submit' value='".'Kill'."'>\n";}echo'<input type="hidden" name="token" value="',$R,'">
</form>
';}elseif(isset($_GET["select"])){$a=$_GET["select"];$P=table_status($a);$u=indexes($a);$n=fields($a);$rc=column_foreign_keys($a);$Ud="";if($P["Oid"]=="t"){$Ud=($v=="sqlite"?"rowid":"oid");$u[]=array("type"=>"PRIMARY","columns"=>array($Ud));}parse_str($_COOKIE["adminer_import"],$qa);$jf=array();$g=array();$cg=null;foreach($n
as$w=>$m){$A=$b->fieldName($m);if(isset($m["privileges"]["select"])&&$A!=""){$g[$w]=html_entity_decode(strip_tags($A),ENT_QUOTES);if(is_shortable($m))$cg=$b->selectLengthProcess();}$jf+=$m["privileges"];}list($K,$Bc)=$b->selectColumnsProcess($g,$u);$Vc=count($Bc)<count($K);$Z=$b->selectSearchProcess($n,$u);$ie=$b->selectOrderProcess($n,$u);$x=$b->selectLimitProcess();$wc=($K?implode(", ",$K):"*".($Ud?", $Ud":"")).convert_fields($g,$n,$K)."\nFROM ".table($a);$Cc=($Bc&&$Vc?"\nGROUP BY ".implode(", ",$Bc):"").($ie?"\nORDER BY ".implode(", ",$ie):"");if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$_g=>$I){$wa=convert_field($n[key($I)]);echo$h->result("SELECT".limit($wa?$wa:idf_escape(key($I))." FROM ".table($a)," WHERE ".where_check($_g,$n).($Z?" AND ".implode(" AND ",$Z):"").($ie?" ORDER BY ".implode(", ",$ie):""),1));}exit;}if($_POST&&!$l){$Rg=$Z;if(is_array($_POST["check"]))$Rg[]="((".implode(") OR (",array_map('where_check',$_POST["check"]))."))";$Rg=($Rg?"\nWHERE ".implode(" AND ",$Rg):"");$Ke=$Bg=null;foreach($u
as$t){if($t["type"]=="PRIMARY"){$Ke=array_flip($t["columns"]);$Bg=($K?$Ke:array());break;}}foreach((array)$Bg
as$w=>$W){if(in_array(idf_escape($w),$K))unset($Bg[$w]);}if($_POST["export"]){cookie("adminer_import","output=".urlencode($_POST["output"])."&format=".urlencode($_POST["format"]));dump_headers($a);$b->dumpTable($a,"");if(!is_array($_POST["check"])||$Bg===array())$F="SELECT $wc$Rg$Cc";else{$yg=array();foreach($_POST["check"]as$W)$yg[]="(SELECT".limit($wc,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$n).$Cc,1).")";$F=implode(" UNION ALL ",$yg);}$b->dumpData($a,"table",$F);exit;}if(!$b->selectEmailProcess($Z,$rc)){if($_POST["save"]||$_POST["delete"]){$G=true;$ra=0;$F=table($a);$M=array();if(!$_POST["delete"]){foreach($g
as$A=>$W){$W=process_input($n[$A]);if($W!==null){if($_POST["clone"])$M[idf_escape($A)]=($W!==false?$W:idf_escape($A));elseif($W!==false)$M[]=idf_escape($A)." = $W";}}$F.=($_POST["clone"]?" (".implode(", ",array_keys($M)).")\nSELECT ".implode(", ",$M)."\nFROM ".table($a):" SET\n".implode(",\n",$M));}if($_POST["delete"]||$M){$Wa="UPDATE";if($_POST["delete"]){$Wa="DELETE";$F="FROM $F";}if($_POST["clone"]){$Wa="INSERT";$F="INTO $F";}if($_POST["all"]||($Bg===array()&&$_POST["check"])||$Vc){$G=queries("$Wa $F$Rg");$ra=$h->affected_rows;}else{foreach((array)$_POST["check"]as$W){$G=queries($Wa.limit1($F,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$n)));if(!$G)break;$ra+=$h->affected_rows;}}}$Cd=lang(array('%d item has been affected.','%d items have been affected.'),$ra);if($_POST["clone"]&&$G&&$ra==1){$jd=last_id();if($jd)$Cd=sprintf('Item%s has been inserted.'," $jd");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$Cd,$G);}elseif(!$_POST["import"]){if(!$_POST["val"])$l='Ctrl+click on a value to modify it.';else{$G=true;$ra=0;foreach($_POST["val"]as$_g=>$I){$M=array();foreach($I
as$w=>$W){$w=bracket_escape($w,1);$M[]=idf_escape($w)." = ".(ereg('char|text',$n[$w]["type"])||$W!=""?$b->processInput($n[$w],$W):"NULL");}$F=table($a)." SET ".implode(", ",$M);$Qg=" WHERE ".where_check($_g,$n).($Z?" AND ".implode(" AND ",$Z):"");$G=queries("UPDATE".($Vc?" $F$Qg":limit1($F,$Qg)));if(!$G)break;$ra+=$h->affected_rows;}queries_redirect(remove_from_uri(),lang(array('%d item has been affected.','%d items have been affected.'),$ra),$G);}}elseif(is_string($kc=get_file("csv_file",true))){cookie("adminer_import","output=".urlencode($qa["output"])."&format=".urlencode($_POST["separator"]));$G=true;$Va=array_keys($n);preg_match_all('~(?>"[^"]*"|[^"\\r\\n]+)+~',$kc,$vd);$ra=count($vd[0]);begin();$wf=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));foreach($vd[0]as$w=>$W){preg_match_all("~((?>\"[^\"]*\")+|[^$wf]*)$wf~",$W.$wf,$wd);if(!$w&&!array_diff($wd[1],$Va)){$Va=$wd[1];$ra--;}else{$M=array();foreach($wd[1]as$q=>$Sa)$M[idf_escape($Va[$q])]=($Sa==""&&$n[$Va[$q]]["null"]?"NULL":q(str_replace('""','"',preg_replace('~^"|"$~','',$Sa))));$G=insert_update($a,$M,$Ke);if(!$G)break;}}if($G)queries("COMMIT");queries_redirect(remove_from_uri("page"),lang(array('%d row has been imported.','%d rows have been imported.'),$ra),$G);queries("ROLLBACK");}else$l=upload_error($kc);}}$Qf=$b->tableName($P);if(is_ajax())ob_start();page_header('Select'.": $Qf",$l);$M=null;if(isset($jf["insert"])){$M="";foreach((array)$_GET["where"]as$W){if(count($rc[$W["col"]])==1&&($W["op"]=="="||(!$W["op"]&&!ereg('[_%]',$W["val"]))))$M.="&set".urlencode("[".bracket_escape($W["col"])."]")."=".urlencode($W["val"]);}}$b->selectLinks($P,$M);if(!$g)echo"<p class='error'>".'Unable to select the table'.($n?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?'<input type="hidden" name="db" value="'.h(DB).'">'.(isset($_GET["ns"])?'<input type="hidden" name="ns" value="'.h($_GET["ns"]).'">':""):"");echo'<input type="hidden" name="select" value="'.h($a).'">',"</div>\n";$b->selectColumnsPrint($K,$g);$b->selectSearchPrint($Z,$g,$u);$b->selectOrderPrint($ie,$g,$u);$b->selectLimitPrint($x);$b->selectLengthPrint($cg);$b->selectActionPrint($u);echo"</form>\n";$C=$_GET["page"];if($C=="last"){$uc=$h->result("SELECT COUNT(*) FROM ".table($a).($Z?" WHERE ".implode(" AND ",$Z):""));$C=floor(max(0,$uc-1)/$x);}$F=$b->selectQueryBuild($K,$Z,$Bc,$ie,$x,$C);if(!$F)$F="SELECT".limit((+$x&&$Bc&&$Vc&&$v=="sql"?"SQL_CALC_FOUND_ROWS ":"").$wc,($Z?"\nWHERE ".implode(" AND ",$Z):"").$Cc,($x!=""?+$x:null),($C?$x*$C:0),"\n");echo$b->selectQuery($F);$G=$h->query($F);if(!$G)echo"<p class='error'>".error()."\n";else{if($v=="mssql"&&$C)$G->seek($x*$C);$Mb=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$J=array();while($I=$G->fetch_assoc()){if($C&&$v=="oracle")unset($I["RNUM"]);$J[]=$I;}if($_GET["page"]!="last")$uc=(+$x&&$Bc&&$Vc?($v=="sql"?$h->result(" SELECT FOUND_ROWS()"):$h->result("SELECT COUNT(*) FROM ($F) x")):count($J));if(!$J)echo"<p class='message'>".'No rows.'."\n";else{$Ca=$b->backwardKeys($a,$Qf);echo"<table id='table' cellspacing='0' class='nowrap checkable' onclick='tableClick(event);' ondblclick='tableClick(event, true);' onkeydown='return editingKeydown(event);'>\n","<thead><tr>".(!$Bc&&$K?"":"<td><input type='checkbox' id='all-page' onclick='formCheck(this, /check/);'> <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".'edit'."</a>");$Nd=array();$zc=array();reset($K);$Ve=1;foreach($J[0]as$w=>$W){if($w!=$Ud){$W=$_GET["columns"][key($K)];$m=$n[$K?($W?$W["col"]:current($K)):$w];$A=($m?$b->fieldName($m,$Ve):"*");if($A!=""){$Ve++;$Nd[$w]=$A;$f=idf_escape($w);$Jc=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($w);$ub="&desc%5B0%5D=1";echo'<th onmouseover="columnMouse(this);" onmouseout="columnMouse(this, \' hidden\');">','<a href="'.h($Jc.($ie[0]==$f||$ie[0]==$w||(!$ie&&$Vc&&$Bc[0]==$f)?$ub:'')).'">';echo(!$K||$W?apply_sql_function($W["fun"],$A):h(current($K)))."</a>";echo"<span class='column hidden'>","<a href='".h($Jc.$ub)."' title='".'descending'."' class='text'> ‚Üì</a>";if(!$W["fun"])echo'<a href="#fieldset-search" onclick="selectSearch(\''.h(js_escape($w)).'\'); return false;" title="'.'Search'.'" class="text jsonly"> =</a>';echo"</span>";}$zc[$w]=$W["fun"];next($K);}}$pd=array();if($_GET["modify"]){foreach($J
as$I){foreach($I
as$w=>$W)$pd[$w]=max($pd[$w],min(40,strlen(utf8_decode($W))));}}echo($Ca?"<th>".'Relations':"")."</thead>\n";if(is_ajax()){if($x%2==1&&$C%2==1)odd();ob_end_clean();}foreach($b->rowDescriptions($J,$rc)as$Md=>$I){$zg=unique_array($J[$Md],$u);if(!$zg){$zg=array();foreach($J[$Md]as$w=>$W){if(!preg_match('~^(COUNT\\((\\*|(DISTINCT )?`(?:[^`]|``)+`)\\)|(AVG|GROUP_CONCAT|MAX|MIN|SUM)\\(`(?:[^`]|``)+`\\))$~',$w))$zg[$w]=$W;}}$_g="";foreach($zg
as$w=>$W){if(strlen($W)>64){$w="MD5(".(strpos($w,'(')?$w:idf_escape($w)).")";$W=md5($W);}$_g.="&".($W!==null?urlencode("where[".bracket_escape($w)."]")."=".urlencode($W):"null%5B%5D=".urlencode($w));}echo"<tr".odd().">".(!$Bc&&$K?"":"<td>".checkbox("check[]",substr($_g,1),in_array(substr($_g,1),(array)$_POST["check"]),"","this.form['all'].checked = false; formUncheck('all-page');").($Vc||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$_g)."'>".'edit'."</a>"));foreach($I
as$w=>$W){if(isset($Nd[$w])){$m=$n[$w];if($W!=""&&(!isset($Mb[$w])||$Mb[$w]!=""))$Mb[$w]=(is_mail($W)?$Nd[$w]:"");$y="";$W=$b->editVal($W,$m);if($W!==null){if(ereg('blob|bytea|raw|file',$m["type"])&&$W!="")$y=ME.'download='.urlencode($a).'&field='.urlencode($w).$_g;if($W==="")$W="&nbsp;";elseif($cg!=""&&is_shortable($m))$W=shorten_utf8($W,max(0,+$cg));else$W=h($W);if(!$y){foreach((array)$rc[$w]as$o){if(count($rc[$w])==1||end($o["source"])==$w){$y="";foreach($o["source"]as$q=>$Bf)$y.=where_link($q,$o["target"][$q],$J[$Md][$Bf]);$y=($o["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\\1'.urlencode($o["db"]),ME):ME).'select='.urlencode($o["table"]).$y;if(count($o["source"])==1)break;}}}if($w=="COUNT(*)"){$y=ME."select=".urlencode($a);$q=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$zg))$y.=where_link($q++,$V["col"],$V["val"],$V["op"]);}foreach($zg
as$bd=>$V)$y.=where_link($q++,$bd,$V);}}if(!$y&&($y=$b->selectLink($I[$w],$m))===null){if(is_mail($I[$w]))$y="mailto:$I[$w]";if($Se=is_url($I[$w]))$y=($Se=="http"&&$ba?$I[$w]:"$Se://www.adminer.org/redirect/?url=".urlencode($I[$w]));}$r=h("val[$_g][".bracket_escape($w)."]");$X=$_POST["val"][$_g][bracket_escape($w)];$Ec=h($X!==null?$X:$I[$w]);$td=strpos($W,"<i>...</i>");$Ib=is_utf8($W)&&$J[$Md][$w]==$I[$w]&&!$zc[$w];$bg=ereg('text|lob',$m["type"]);echo(($_GET["modify"]&&$Ib)||$X!==null?"<td>".($bg?"<textarea name='$r' cols='30' rows='".(substr_count($I[$w],"\n")+1)."'>$Ec</textarea>":"<input name='$r' value='$Ec' size='$pd[$w]'>"):"<td id='$r' onclick=\"selectClick(this, event, ".($td?2:($bg?1:0)).($Ib?"":", '".h('Use edit link to modify this value.')."'").");\">".$b->selectVal($W,$y,$m));}}if($Ca)echo"<td>";$b->backwardKeysPrint($Ca,$J[$Md]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n",(!$Bc&&$K?"":"<script type='text/javascript'>tableCheck();</script>\n");}if(($J||$C)&&!is_ajax()){$Wb=true;if($_GET["page"]!="last"&&+$x&&!$Vc&&($uc>=$x||$C)){$uc=found_rows($P,$Z);if($uc<max(1e4,2*($C+1)*$x))$uc=reset(slow_query("SELECT COUNT(*) FROM ".table($a).($Z?" WHERE ".implode(" AND ",$Z):"")));else$Wb=false;}if(+$x&&($uc===false||$uc>$x||$C)){echo"<p class='pages'>";$yd=($uc===false?$C+(count($J)>=$x?2:1):floor(($uc-1)/$x));echo'<a href="'.h(remove_from_uri("page"))."\" onclick=\"pageClick(this.href, +prompt('".'Page'."', '".($C+1)."'), event); return false;\">".'Page'."</a>:",pagination(0,$C).($C>5?" ...":"");for($q=max(1,$C-4);$q<min($yd,$C+5);$q++)echo
pagination($q,$C);if($yd>0){echo($C+5<$yd?" ...":""),($Wb&&$uc!==false?pagination($yd,$C):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$yd'>".'last'."</a>");}echo(($uc===false?count($J)+1:$uc-$C*$x)>$x?' <a href="'.h(remove_from_uri("page")."&page=".($C+1)).'" onclick="return !selectLoadMore(this, '.(+$x).', \''.'Loading'.'\');">'.'Load more data'.'</a>':'');}echo"<p>\n",($uc!==false?"(".($Wb?"":"~ ").lang(array('%d row','%d rows'),$uc).") ":""),checkbox("all",1,0,'whole result')."\n";if($b->selectCommandPrint()){echo'<fieldset><legend>Edit</legend><div>
<input type="submit" value="Save"',($_GET["modify"]?'':' title="'.'Ctrl+click on a value to modify it.'.'" class="jsonly"');?>>
<input type="submit" name="edit" value="Edit">
<input type="submit" name="clone" value="Clone">
<input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure? (' + (this.form['all'].checked ? <?php echo$uc,' : formChecked(this, /check/)) + \')\');">
</div></fieldset>
';}$sc=$b->dumpFormat();foreach((array)$_GET["columns"]as$f){if($f["fun"]){unset($sc['sql']);break;}}if($sc){print_fieldset("export",'Export');$se=$b->dumpOutput();echo($se?html_select("output",$se,$qa["output"])." ":""),html_select("format",$sc,$qa["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}}if($b->selectImportPrint()){print_fieldset("import",'Import',!$J);echo"<input type='file' name='csv_file'> ",html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$qa["format"],1);echo" <input type='submit' name='import' value='".'Import'."'>","</div></fieldset>\n";}$b->selectEmailPrint(array_filter($Mb,'strlen'),$g);echo"<p><input type='hidden' name='token' value='$R'></p>\n","</form>\n";}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$Ff=isset($_GET["status"]);page_header($Ff?'Status':'Variables');$Lg=($Ff?show_status():show_variables());if(!$Lg)echo"<p class='message'>".'No rows.'."\n";else{echo"<table cellspacing='0'>\n";foreach($Lg
as$w=>$W){echo"<tr>","<th><code class='jush-".$v.($Ff?"status":"set")."'>".h($w)."</code>","<td>".nbsp($W);}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$Nf=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$A=>$P){$r=js_escape($A);json_row("Comment-$r",nbsp($P["Comment"]));if(!is_view($P)){foreach(array("Engine","Collation")as$w)json_row("$w-$r",nbsp($P[$w]));foreach($Nf+array("Auto_increment"=>0,"Rows"=>0)as$w=>$W){if($P[$w]!=""){$W=number_format($P[$w],0,'.',',');json_row("$w-$r",($w=="Rows"&&$W&&$P["Engine"]==($Df=="pgsql"?"table":"InnoDB")?"~ $W":$W));if(isset($Nf[$w]))$Nf[$w]+=($P["Engine"]!="InnoDB"||$w!="Data_free"?$P[$w]:0);}elseif(array_key_exists($w,$P))json_row("$w-$r");}}}foreach($Nf
as$w=>$W)json_row("sum-$w",number_format($W,0,'.',','));json_row("");}elseif($_GET["script"]=="kill")$h->query("KILL ".(+$_POST["kill"]));else{foreach(count_tables($b->databases())as$k=>$W)json_row("tables-".js_escape($k),$W);json_row("");}exit;}else{$Wf=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Wf&&!$l&&!$_POST["search"]){$G=true;$Cd="";if($v=="sql"&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$G=truncate_tables($_POST["tables"]);$Cd='Tables have been truncated.';}elseif($_POST["move"]){$G=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Cd='Tables have been moved.';}elseif($_POST["copy"]){$G=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Cd='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$G=drop_views($_POST["views"]);if($G&&$_POST["tables"])$G=drop_tables($_POST["tables"]);$Cd='Tables have been dropped.';}elseif($v!="sql"){$G=($v=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?"":" ANALYZE"),$_POST["tables"]));$Cd='Tables have been optimized.';}elseif(!$_POST["tables"])$Cd='No tables.';elseif($G=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('idf_escape',$_POST["tables"])))){while($I=$G->fetch_assoc())$Cd.="<b>".h($I["Table"])."</b>: ".h($I["Msg_text"])."<br>";}queries_redirect(substr(ME,0,-1),$Cd,$G);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$l,true);if($b->homepage()){if($_GET["ns"]!==""){echo"<h3 id='tables-views'>".'Tables and views'."</h3>\n";$Vf=tables_list();if(!$Vf)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n","<p>".'Search data in tables'.": <input type='search' name='query' value='".h($_POST["query"])."'> <input type='submit' name='search' value='".'Search'."'>\n";if($_POST["search"]&&$_POST["query"]!="")search_tables();echo"<table cellspacing='0' class='nowrap checkable' onclick='tableClick(event);' ondblclick='tableClick(event, true);'>\n",'<thead><tr class="wrap"><td><input id="check-all" type="checkbox" onclick="formCheck(this, /^(tables|views)\[/);">','<th>'.'Table','<td>'.'Engine','<td>'.'Collation','<td>'.'Data Length','<td>'.'Index Length','<td>'.'Data Free','<td>'.'Auto Increment','<td>'.'Rows',(support("comment")?'<td>'.'Comment':''),"</thead>\n";foreach($Vf
as$A=>$S){$Ng=($S!==null&&!eregi("table",$S));echo'<tr'.odd().'><td>'.checkbox(($Ng?"views[]":"tables[]"),$A,in_array($A,$Wf,true),"","formUncheck('check-all');"),'<th><a href="'.h(ME).'table='.urlencode($A).'" title="'.'Show structure'.'">'.h($A).'</a>';if($Ng){echo'<td colspan="6"><a href="'.h(ME)."view=".urlencode($A).'" title="'.'Alter view'.'">'.'View'.'</a>','<td align="right"><a href="'.h(ME)."select=".urlencode($A).'" title="'.'Select data'.'">?</a>';}else{foreach(array("Engine"=>array(),"Collation"=>array(),"Data_length"=>array("create",'Alter table'),"Index_length"=>array("indexes",'Alter indexes'),"Data_free"=>array("edit",'New item'),"Auto_increment"=>array("auto_increment=1&create",'Alter table'),"Rows"=>array("select",'Select data'),)as$w=>$y)echo($y?"<td align='right'><a href='".h(ME."$y[0]=").urlencode($A)."' id='$w-".h($A)."' title='$y[1]'>?</a>":"<td id='$w-".h($A)."'>&nbsp;");}echo(support("comment")?"<td id='Comment-".h($A)."'>&nbsp;":"");}echo"<tr><td>&nbsp;<th>".sprintf('%d in total',count($Vf)),"<td>".nbsp($v=="sql"?$h->result("SELECT @@storage_engine"):""),"<td>".nbsp(db_collation(DB,collations()));foreach(array("Data_length","Index_length","Data_free")as$w)echo"<td align='right' id='sum-$w'>&nbsp;";echo"</table>\n","<script type='text/javascript'>tableCheck();</script>\n";if(!information_schema(DB)){echo"<p>".(ereg('^(sql|sqlite|pgsql)$',$v)?($v!="sqlite"?"<input type='submit' value='".'Analyze'."'> ":"")."<input type='submit' name='optimize' value='".'Optimize'."'> ":"").($v=="sql"?"<input type='submit' name='check' value='".'Check'."'> <input type='submit' name='repair' value='".'Repair'."'> ":"")."<input type='submit' name='truncate' value='".'Truncate'."'".confirm("formChecked(this, /tables/)")."> <input type='submit' name='drop' value='".'Drop'."'".confirm("formChecked(this, /tables|views/)").">\n";$j=(support("scheme")?schemas():$b->databases());if(count($j)!=1&&$v!="sqlite"){$k=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo"<p>".'Move to other database'.": ",($j?html_select("target",$j,$k):'<input name="target" value="'.h($k).'" autocapitalize="off">')," <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'>":""),"\n";}echo"<input type='hidden' name='token' value='$R'>\n";}echo"</form>\n";}echo'<p><a href="'.h(ME).'create=">'.'Create table'."</a>\n";if(support("view"))echo'<a href="'.h(ME).'view=">'.'Create view'."</a>\n";if(support("routine")){echo"<h3 id='routines'>".'Routines'."</h3>\n";$nf=routines();if($nf){echo"<table cellspacing='0'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td>&nbsp;</thead>\n";odd('');foreach($nf
as$I){echo'<tr'.odd().'>','<th><a href="'.h(ME).($I["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($I["ROUTINE_NAME"]).'">'.h($I["ROUTINE_NAME"]).'</a>','<td>'.h($I["ROUTINE_TYPE"]),'<td>'.h($I["DTD_IDENTIFIER"]),'<td><a href="'.h(ME).($I["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($I["ROUTINE_NAME"]).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p>'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a> ':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n";}if(support("sequence")){echo"<h3 id='sequences'>".'Sequences'."</h3>\n";$xf=get_vals("SELECT sequence_name FROM information_schema.sequences WHERE sequence_schema = current_schema()");if($xf){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Name'."</thead>\n";odd('');foreach($xf
as$W)echo"<tr".odd()."><th><a href='".h(ME)."sequence=".urlencode($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p><a href='".h(ME)."sequence='>".'Create sequence'."</a>\n";}if(support("type")){echo"<h3 id='user-types'>".'User types'."</h3>\n";$Hg=types();if($Hg){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Name'."</thead>\n";odd('');foreach($Hg
as$W)echo"<tr".odd()."><th><a href='".h(ME)."type=".urlencode($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p><a href='".h(ME)."type='>".'Create type'."</a>\n";}if(support("event")){echo"<h3 id='events'>".'Events'."</h3>\n";$J=get_rows("SHOW EVENTS");if($J){echo"<table cellspacing='0'>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."<td></thead>\n";foreach($J
as$I){echo"<tr>","<th>".h($I["Name"]),"<td>".($I["Execute at"]?'At given time'."<td>".$I["Execute at"]:'Every'." ".$I["Interval value"]." ".$I["Interval field"]."<td>$I[Starts]"),"<td>$I[Ends]",'<td><a href="'.h(ME).'event='.urlencode($I["Name"]).'">'.'Alter'.'</a>';}echo"</table>\n";$Vb=$h->result("SELECT @@event_scheduler");if($Vb&&$Vb!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Vb)."\n";}echo'<p><a href="'.h(ME).'event=">'.'Create event'."</a>\n";}if($Vf)echo"<script type='text/javascript'>ajaxSetHtml('".js_escape(ME)."script=db');</script>\n";}}}page_footer();