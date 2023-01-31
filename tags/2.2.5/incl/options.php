<?php

/**
 * @author BAKKBONE Australia
 * @package BkfPluginOptions
 * @license GNU General Public License (GPL) 3.0
**/

defined("BKF_EXEC") or die("Silence is golden");

class BkfPluginOptions{

	private $bkf_options_setting = array();
	private $bkf_features_setting = array();
	private $bkf_advanced_setting = array();

	function __construct()
	{
		if(is_admin()){
			$this->bkf_options_setting = get_option("bkf_options_setting");
			$this->bkf_features_setting = get_option("bkf_features_setting");
			$this->bkf_advanced_setting = get_option("bkf_advanced_setting");
			add_action("admin_menu",array($this,"bkfAddOptionsPageOption"),2);
			add_action("admin_init",array($this,"bkfAddOptionsPageInit"));
			add_action("admin_footer",array($this,"bkfOptionsAdminFooter"));
			add_action("admin_enqueue_scripts",array($this,"bkfOptionsAdminEnqueueScripts"));
		}
	}

	function bkfAddOptionsPageOption()
	{
		add_menu_page(
			null, //$page_title
			__("Florist Options","bakkbone-florist-companion"), //$menu_title
			"manage_options", //$capability
			"bkf_options",//$menu_slug
			null,//$function
			'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxNS43NzIzNTE3NzlweCIgaGVpZ2h0PSIyMHB4IiB2aWV3Qm94PSIwIDAgOTcgMTIzIj4NCiAgPHBhdGggaWQ9ImJvdXF1ZXQiIGZpbGw9IiNjY2NjY2MiIHN0cm9rZT0iI2NjY2NjYyIgc3Ryb2tlLXdpZHRoPSIxIiBkPSJNIDEzLjgxLDYxLjQ1IEMgMjAuMTEsNjguNzMgMjUuNzYsNzUuNTggMjkuMzIsODIuNTUgMjkuMzIsODIuNTUgMTcuMjAsODQuOTcgMTcuMjAsODQuOTcgMTcuMjAsODQuOTcgMjUuNTIsODkuMzggMjUuNTIsODkuMzggMjUuNTIsODkuMzggMjEuNjMsMTAwLjAxIDIxLjYzLDEwMC4wMSAyMS42MywxMDAuMDEgMjEuNjMsMTAwLjAxIDIxLjYzLDEwMC4wMSAyMS42MywxMDAuMDEgMzIuNjUsOTEuNzcgMzIuNjUsOTEuNzcgMzQuMjAsOTkuNDQgMzIuNjUsMTA3LjU0IDI2LjI0LDExNi43NCAzMC43MiwxMTYuMjEgMzUuNjUsMTE3LjE5IDM4LjI4LDEyMi40NSA0MS4xMywxMTguODAgNDkuMjksMTE4LjM1IDUxLjg1LDEyMy4wMCA1Mi42NiwxMTguMTQgNTYuODksMTE0Ljk5IDYxLjc4LDExNS4zMSA1Ni40NiwxMDYuNzEgNTMuOTMsOTkuMTcgNTQuNzcsOTEuNjIgNTQuNzcsOTEuNjIgNjUuNjksMTAwLjQxIDY1LjY5LDEwMC40MSA2NS42OSwxMDAuNDEgNjUuNjksMTAwLjQxIDY1LjY5LDEwMC40MSA2NS42OSwxMDAuNDEgNjIuMTksODkuNjUgNjIuMTksODkuNjUgNjIuMTksODkuNjUgNzAuNjYsODUuNTQgNzAuNjYsODUuNTQgNzAuNjYsODUuNTQgNTcuNDksODIuNDIgNTcuNDksODIuNDIgNjAuOTMsNzUuMTggNjcuNTQsNjcuNTUgNzcuODIsNTguNjUgNjUuOTcsNTAuMDcgNjYuMTgsNTkuODAgNTkuMTAsNTkuNDUgNTIuMDMsNTkuMTAgNTAuMDUsNTMuMjUgNDQuNjksNTIuOTEgMzUuODksNTIuMzYgMzQuMjksNjMuNTkgMjQuMjMsNTguNjQgMTkuNjksNTYuNDEgMTcuNDYsNTcuNjEgMTMuODEsNjEuNDUgMTMuODEsNjEuNDUgMTMuODEsNjEuNDUgMTMuODEsNjEuNDUgWiBNIDI4LjU0LDU3LjI3IEMgMjQuMDMsNTEuMDAgMTkuOTMsNDQuMDkgMTguOTcsMzYuNjkgMTYuNzUsNDMuNDYgOC42MSwzOS43OSAxMC4zNCwzNC41NCAxMC4zNCwzNC41NCAxMS45NywyOS42MCAxMS45NywyOS42MCAxMS45NywyOS42MCA3LjMyLDMxLjk0IDcuMzIsMzEuOTQgMi4xMSwzNC41NyAtMS44OSwyNi42NSAzLjMyLDI0LjAyIDMuMzIsMjQuMDIgNy45NywyMS42NyA3Ljk3LDIxLjY3IDcuOTcsMjEuNjcgMy4wMiwyMC4wNCAzLjAyLDIwLjA0IC0yLjUyLDE4LjIyIDAuMjUsOS43OSA1Ljc5LDExLjYxIDUuNzksMTEuNjEgMTAuNzQsMTMuMjQgMTAuNzQsMTMuMjQgMTAuNzQsMTMuMjQgOC4zOSw4LjU5IDguMzksOC41OSA1Ljc0LDMuMzMgMTMuNjksLTAuNjMgMTYuMzIsNC41OSAxNi4zMiw0LjU5IDE4LjY3LDkuMjQgMTguNjcsOS4yNCAxOC42Nyw5LjI0IDIwLjMwLDQuMjkgMjAuMzAsNC4yOSAyMi4xMiwtMS4yNSAzMC41NSwxLjUzIDI4LjczLDcuMDYgMjguNzMsNy4wNiAyNy4xMCwxMi4wMSAyNy4xMCwxMi4wMSAyNy4xMCwxMi4wMSAzMS43Niw5LjY2IDMxLjc2LDkuNjYgMzYuOTYsNy4wNCA0MC45NywxNC45NiAzNS43NiwxNy41OSAzNS43NiwxNy41OSAzMS4xMCwxOS45NCAzMS4xMCwxOS45NCAzMS4xMCwxOS45NCAzNi4wNSwyMS41NiAzNi4wNSwyMS41NiA0MS41OSwyMy4zOSAzOC44MiwzMS44MiAzMy4yOCwyOS45OSAzMy4yOCwyOS45OSAyOC4zMywyOC4zNiAyOC4zMywyOC4zNiAyOC4zMywyOC4zNiAzMC42OCwzMy4wMiAzMC42OCwzMy4wMiAzMy4yMiwzOC4wNSAyNS42MSw0Mi42NyAyMi41MSwzNi41NCAyMy45Niw0MS4yNiAyOC44Niw0OC4wNSAzMy43OSw1NS4xNCAzMi4yNCw1Ni4zNSAzMC42NSw1Ny4zNyAyOC41NCw1Ny4yNyAyOC41NCw1Ny4yNyAyOC41NCw1Ny4yNyAyOC41NCw1Ny4yNyBaIE0gNjkuNjksNTIuNDMgQyA3My42Nyw0Ni45MyA3Ny4xMyw0MC45MiA3OC4xMiwzNC40NyA4MC4xMyw0MS4wOCA4OC4xMCwzNy43MSA4Ni41NCwzMi41OCA4Ni41NCwzMi41OCA4NS4wOCwyNy43NSA4NS4wOCwyNy43NSA4NS4wOCwyNy43NSA4OS41MywzMC4xMyA4OS41MywzMC4xMyA5NC41MiwzMi44MCA5OC41OCwyNS4yMSA5My42MCwyMi41NCA5My42MCwyMi41NCA4OS4xNCwyMC4xNiA4OS4xNCwyMC4xNiA4OS4xNCwyMC4xNiA5My45OCwxOC43MCA5My45OCwxOC43MCA5OS4zOSwxNy4wNiA5Ni45MCw4LjgyIDkxLjQ4LDEwLjQ2IDkxLjQ4LDEwLjQ2IDg2LjY1LDExLjkyIDg2LjY1LDExLjkyIDg2LjY1LDExLjkyIDg5LjAzLDcuNDYgODkuMDMsNy40NiA5MS43MywyLjQzIDg0LjExLC0xLjU5IDgxLjQ0LDMuNDAgODEuNDQsMy40MCA3OS4wNSw3Ljg1IDc5LjA1LDcuODUgNzkuMDUsNy44NSA3Ny41OSwzLjAyIDc3LjU5LDMuMDIgNzUuOTUsLTIuMzkgNjcuNzEsMC4xMCA2OS4zNSw1LjUxIDY5LjM1LDUuNTEgNzAuODEsMTAuMzUgNzAuODEsMTAuMzUgNzAuODEsMTAuMzUgNjYuMzUsNy45NiA2Ni4zNSw3Ljk2IDYxLjM2LDUuMzAgNTcuMzAsMTIuODkgNjIuMjksMTUuNTYgNjIuMjksMTUuNTYgNjYuNzUsMTcuOTQgNjYuNzUsMTcuOTQgNjYuNzUsMTcuOTQgNjEuOTEsMTkuNDAgNjEuOTEsMTkuNDAgNTYuNTAsMjEuMDQgNTguOTksMjkuMjggNjQuNDEsMjcuNjQgNjQuNDEsMjcuNjQgNjkuMjUsMjYuMTggNjkuMjUsMjYuMTggNjkuMjUsMjYuMTggNjYuODYsMzAuNjQgNjYuODYsMzAuNjQgNjQuMjgsMzUuNDUgNzEuNTUsNDAuMTIgNzQuNzAsMzQuMjQgNzIuODEsMzkuODcgNjUuNzMsNDguNDYgNTkuODEsNTYuOTAgNjEuMjQsNTYuNzcgNjIuMzQsNTUuOTUgNjMuNDMsNTUuMTQgNjUuMjcsNTMuNzcgNjcuMDcsNTIuNDMgNjkuNjksNTIuNDMgNjkuNjksNTIuNDMgNjkuNjksNTIuNDMgNjkuNjksNTIuNDMgWiBNIDgwLjA2LDE1LjExIEMgODIuMjQsMTYuMjggODMuMDYsMTguOTkgODEuODksMjEuMTYgODAuNzMsMjMuMzQgNzguMDIsMjQuMTYgNzUuODQsMjMuMDAgNzMuNjYsMjEuODMgNzIuODQsMTkuMTIgNzQuMDEsMTYuOTUgNzUuMTcsMTQuNzcgNzcuODgsMTMuOTUgODAuMDYsMTUuMTEgODAuMDYsMTUuMTEgODAuMDYsMTUuMTEgODAuMDYsMTUuMTEgODAuMDYsMTUuMTEgODAuMDYsMTUuMTEgODAuMDYsMTUuMTEgWiBNIDQ2LjQyLDUwLjYwIEMgNDYuMzcsNDkuMTAgNDYuNDcsNDcuNjIgNDYuNzYsNDYuMTcgNDQuMDQsNDkuODAgMzkuODgsNDUuOTMgNDEuOTksNDMuMTIgNDEuOTksNDMuMTIgNDMuOTcsNDAuNDcgNDMuOTcsNDAuNDcgNDMuOTcsNDAuNDcgNDAuNjksNDAuOTQgNDAuNjksNDAuOTQgMzcuMDIsNDEuNDcgMzYuMjIsMzUuODggMzkuODksMzUuMzUgMzkuODksMzUuMzUgNDMuMTcsMzQuODggNDMuMTcsMzQuODggNDMuMTcsMzQuODggNDAuNTIsMzIuODkgNDAuNTIsMzIuODkgMzcuNTUsMzAuNjcgNDAuOTMsMjYuMTUgNDMuOTAsMjguMzcgNDMuOTAsMjguMzcgNDYuNTYsMzAuMzYgNDYuNTYsMzAuMzYgNDYuNTYsMzAuMzYgNDYuMDksMjcuMDggNDYuMDksMjcuMDggNDUuNTUsMjMuMzcgNTEuMTUsMjIuNTkgNTEuNjgsMjYuMjcgNTEuNjgsMjYuMjcgNTIuMTUsMjkuNTUgNTIuMTUsMjkuNTUgNTIuMTUsMjkuNTUgNTQuMTQsMjYuOTAgNTQuMTQsMjYuOTAgNTYuMzYsMjMuOTMgNjAuODksMjcuMzIgNTguNjYsMzAuMjkgNTguNjYsMzAuMjkgNTYuNjcsMzIuOTQgNTYuNjcsMzIuOTQgNTYuNjcsMzIuOTQgNTkuOTYsMzIuNDcgNTkuOTYsMzIuNDcgNjMuNjMsMzEuOTQgNjQuNDQsMzcuNTMgNjAuNzYsMzguMDYgNjAuNzYsMzguMDYgNTcuNDgsMzguNTMgNTcuNDgsMzguNTMgNTcuNDgsMzguNTMgNjAuMTMsNDAuNTIgNjAuMTMsNDAuNTIgNjMuMTAsNDIuNzQgNTkuNzEsNDcuMjYgNTYuNzQsNDUuMDQgNTYuNzQsNDUuMDQgNTQuMDksNDMuMDUgNTQuMDksNDMuMDUgNTQuMDksNDMuMDUgNTQuNTYsNDYuMzMgNTQuNTYsNDYuMzMgNTUuMDcsNDkuODggNDkuNTQsNTEuMTMgNDguOTIsNDYuODAgNDguODgsNDguMTggNDkuMTcsNDkuOTUgNDkuNjMsNTEuOTQgNDkuNjMsNTEuOTQgNDkuNTksNTEuOTEgNDkuNTksNTEuOTEgNDkuNTksNTEuOTEgNDkuNTcsNTEuOTAgNDkuNTcsNTEuOTAgNDkuNTcsNTEuOTAgNDkuNTQsNTEuODggNDkuNTQsNTEuODggNDkuNTQsNTEuODggNDkuNDksNTEuODUgNDkuNDksNTEuODUgNDkuNDksNTEuODUgNDkuNDcsNTEuODQgNDkuNDcsNTEuODQgNDkuNDcsNTEuODQgNDkuNDUsNTEuODMgNDkuNDUsNTEuODMgNDkuNDUsNTEuODMgNDkuNDAsNTEuODAgNDkuNDAsNTEuODAgNDkuNDAsNTEuODAgNDkuMzYsNTEuNzggNDkuMzYsNTEuNzggNDkuMzYsNTEuNzggNDkuMzYsNTEuNzcgNDkuMzYsNTEuNzcgNDkuMzYsNTEuNzcgNDkuMzEsNTEuNzUgNDkuMzEsNTEuNzUgNDkuMzEsNTEuNzUgNDkuMjYsNTEuNzIgNDkuMjYsNTEuNzIgNDkuMjYsNTEuNzIgNDkuMjUsNTEuNzIgNDkuMjUsNTEuNzIgNDkuMjUsNTEuNzIgNDkuMjIsNTEuNzAgNDkuMjIsNTEuNzAgNDkuMjIsNTEuNzAgNDkuMTcsNTEuNjcgNDkuMTcsNTEuNjcgNDkuMTcsNTEuNjcgNDkuMTQsNTEuNjUgNDkuMTQsNTEuNjUgNDkuMTQsNTEuNjUgNDkuMTMsNTEuNjQgNDkuMTMsNTEuNjQgNDkuMTMsNTEuNjQgNDkuMDgsNTEuNjIgNDkuMDgsNTEuNjIgNDkuMDgsNTEuNjIgNDkuMDMsNTEuNTkgNDkuMDMsNTEuNTkgNDkuMDMsNTEuNTkgNDkuMDMsNTEuNTkgNDkuMDMsNTEuNTkgNDkuMDMsNTEuNTkgNDguOTksNTEuNTcgNDguOTksNTEuNTcgNDguOTksNTEuNTcgNDguOTQsNTEuNTQgNDguOTQsNTEuNTQgNDguOTQsNTEuNTQgNDguOTIsNTEuNTQgNDguOTIsNTEuNTQgNDguOTIsNTEuNTQgNDguODksNTEuNTIgNDguODksNTEuNTIgNDguODksNTEuNTIgNDguODQsNTEuNTAgNDguODQsNTEuNTAgNDguODQsNTEuNTAgNDguODEsNTEuNDggNDguODEsNTEuNDggNDguODEsNTEuNDggNDguNzksNTEuNDcgNDguNzksNTEuNDcgNDguNzksNTEuNDcgNDguNzUsNTEuNDUgNDguNzUsNTEuNDUgNDguNzUsNTEuNDUgNDguNzAsNTEuNDIgNDguNzAsNTEuNDIgNDguNzAsNTEuNDIgNDguNzAsNTEuNDIgNDguNzAsNTEuNDIgNDguNzAsNTEuNDIgNDguNjUsNTEuNDAgNDguNjUsNTEuNDAgNDguNjUsNTEuNDAgNDguNjAsNTEuMzggNDguNjAsNTEuMzggNDguNjAsNTEuMzggNDguNTYsNTEuMzUgNDguNTYsNTEuMzUgNDguNTYsNTEuMzUgNDguNTEsNTEuMzMgNDguNTEsNTEuMzMgNDguNTEsNTEuMzMgNDguNDcsNTEuMzEgNDguNDcsNTEuMzEgNDguNDcsNTEuMzEgNDguNDYsNTEuMzEgNDguNDYsNTEuMzEgNDguNDYsNTEuMzEgNDguNDEsNTEuMjkgNDguNDEsNTEuMjkgNDguNDEsNTEuMjkgNDguMzYsNTEuMjYgNDguMzYsNTEuMjYgNDguMzYsNTEuMjYgNDguMzYsNTEuMjYgNDguMzYsNTEuMjYgNDguMzYsNTEuMjYgNDguMzEsNTEuMjQgNDguMzEsNTEuMjQgNDguMzEsNTEuMjQgNDguMjYsNTEuMjIgNDguMjYsNTEuMjIgNDguMjYsNTEuMjIgNDguMjIsNTEuMjAgNDguMjIsNTEuMjAgNDguMjIsNTEuMjAgNDguMTcsNTEuMTggNDguMTcsNTEuMTggNDguMTcsNTEuMTggNDguMDcsNTEuMTMgNDguMDcsNTEuMTMgNDguMDcsNTEuMTMgNDguMDIsNTEuMTEgNDguMDIsNTEuMTEgNDguMDIsNTEuMTEgNDcuOTcsNTEuMDkgNDcuOTcsNTEuMDkgNDcuOTcsNTEuMDkgNDcuOTIsNTEuMDcgNDcuOTIsNTEuMDcgNDcuOTIsNTEuMDcgNDcuODksNTEuMDYgNDcuODksNTEuMDYgNDcuODksNTEuMDYgNDcuODcsNTEuMDUgNDcuODcsNTEuMDUgNDcuODcsNTEuMDUgNDcuODIsNTEuMDMgNDcuODIsNTEuMDMgNDcuODIsNTEuMDMgNDcuNzcsNTEuMDEgNDcuNzcsNTEuMDEgNDcuNzcsNTEuMDEgNDcuNzYsNTEuMDEgNDcuNzYsNTEuMDEgNDcuNzYsNTEuMDEgNDcuNzEsNTAuOTkgNDcuNzEsNTAuOTkgNDcuNzEsNTAuOTkgNDcuNjYsNTAuOTcgNDcuNjYsNTAuOTcgNDcuNjYsNTAuOTcgNDcuNjEsNTAuOTUgNDcuNjEsNTAuOTUgNDcuNjEsNTAuOTUgNDcuNTYsNTAuOTMgNDcuNTYsNTAuOTMgNDcuNTYsNTAuOTMgNDcuNTEsNTAuOTEgNDcuNTEsNTAuOTEgNDcuNTEsNTAuOTEgNDcuNDUsNTAuOTAgNDcuNDUsNTAuOTAgNDcuNDUsNTAuOTAgNDcuNDQsNTAuODkgNDcuNDQsNTAuODkgNDcuNDQsNTAuODkgNDcuNDAsNTAuODggNDcuNDAsNTAuODggNDcuNDAsNTAuODggNDcuMzUsNTAuODYgNDcuMzUsNTAuODYgNDcuMzUsNTAuODYgNDcuMzUsNTAuODYgNDcuMzUsNTAuODYgNDcuMzUsNTAuODYgNDcuMjQsNTAuODMgNDcuMjQsNTAuODMgNDcuMjQsNTAuODMgNDcuMTksNTAuODEgNDcuMTksNTAuODEgNDcuMTksNTAuODEgNDcuMTQsNTAuNzkgNDcuMTQsNTAuNzkgNDcuMTQsNTAuNzkgNDcuMDgsNTAuNzggNDcuMDgsNTAuNzggNDcuMDgsNTAuNzggNDYuOTgsNTAuNzQgNDYuOTgsNTAuNzQgNDYuOTgsNTAuNzQgNDYuOTIsNTAuNzMgNDYuOTIsNTAuNzMgNDYuOTIsNTAuNzMgNDYuODcsNTAuNzEgNDYuODcsNTAuNzEgNDYuODcsNTAuNzEgNDYuODcsNTAuNzEgNDYuODcsNTAuNzEgNDYuODcsNTAuNzEgNDYuODEsNTAuNzAgNDYuODEsNTAuNzAgNDYuODEsNTAuNzAgNDYuNzYsNTAuNjggNDYuNzYsNTAuNjggNDYuNzYsNTAuNjggNDYuNzAsNTAuNjcgNDYuNzAsNTAuNjcgNDYuNzAsNTAuNjcgNDYuNjUsNTAuNjYgNDYuNjUsNTAuNjYgNDYuNjUsNTAuNjYgNDYuNjUsNTAuNjYgNDYuNjUsNTAuNjYgNDYuNjUsNTAuNjYgNDYuNTksNTAuNjQgNDYuNTksNTAuNjQgNDYuNTksNTAuNjQgNDYuNTQsNTAuNjMgNDYuNTQsNTAuNjMgNDYuNTQsNTAuNjMgNDYuNDgsNTAuNjEgNDYuNDgsNTAuNjEgNDYuNDgsNTAuNjEgNDYuNDIsNTAuNjAgNDYuNDIsNTAuNjAgNDYuNDIsNTAuNjAgNDYuNDIsNTAuNjAgNDYuNDIsNTAuNjAgWiBNIDQ5LjkwLDMzLjgwIEMgNDguMzAsMzQuMDMgNDcuMTksMzUuNTIgNDcuNDIsMzcuMTIgNDcuNjUsMzguNzMgNDkuMTMsMzkuODQgNTAuNzQsMzkuNjEgNTIuMzQsMzkuMzggNTMuNDUsMzcuODkgNTMuMjIsMzYuMjkgNTMuMDAsMzQuNjkgNTEuNTEsMzMuNTcgNDkuOTAsMzMuODAgNDkuOTAsMzMuODAgNDkuOTAsMzMuODAgNDkuOTAsMzMuODAgNDkuOTAsMzMuODAgNDkuOTAsMzMuODAgNDkuOTAsMzMuODAgWiBNIDE3LjQ1LDE2LjcwIEMgMTUuMTgsMTcuODQgMTQuMjcsMjAuNjIgMTUuNDEsMjIuODkgMTYuNTYsMjUuMTYgMTkuMzQsMjYuMDcgMjEuNjEsMjQuOTIgMjMuODgsMjMuNzggMjQuNzksMjEuMDAgMjMuNjUsMTguNzMgMjIuNTAsMTYuNDYgMTkuNzMsMTUuNTUgMTcuNDUsMTYuNzAgMTcuNDUsMTYuNzAgMTcuNDUsMTYuNzAgMTcuNDUsMTYuNzAgMTcuNDUsMTYuNzAgMTcuNDUsMTYuNzAgMTcuNDUsMTYuNzAgWiIgLz4NCjwvc3ZnPg==',//icon
			2.1//position
		);
		add_submenu_page(
			"bkf_options",//parent slug
			__("Florist Options","bakkbone-florist-companion"), //$page_title
			__("General Options","bakkbone-florist-companion"), //$menu_title
			"manage_options", //$capability
			"bkf_options",//$menu_slug
			array($this,"bkfOptionsPageContent"),//$content
			10//position
		);
	}

	function bkfOptionsPageContent()
	{
		$this->bkf_options_setting = get_option("bkf_options_setting");
		?>
		<div class="wrap">
			<div class="bkf-box">
			<h1><?php _e("Florist Options","bakkbone-florist-companion") ?></h1>
				<div class="inside">
					<form method="post" action="options.php">
						<?php settings_fields("bkf_options_group"); ?>
						<?php do_settings_sections("bkf-options"); ?>
						<?php submit_button(); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	function bkfAddOptionsPageInit()
	{
		register_setting(
			"bkf_options_group",// group
			"bkf_options_setting", //setting name
			array($this,"bkfAddOptionsSanitize") //sanitize_callback
		);
		register_setting(
			"bkf_options_group",// group
			"bkf_features_setting", //setting name
			array($this,"bkfAddFeaturesSanitize") //sanitize_callback
		);
		register_setting(
			"bkf_options_group",// group
			"bkf_advanced_setting", //setting name
			array($this,"bkfAddAdvancedSanitize") //sanitize_callback
		);
		
		add_settings_section(
			"bkf_options_section", //id
			__("General Settings","bakkbone-florist-companion"), //title
			array($this,"bkfAddOptionsInfo"), //callback
			"bkf-options" //page
		);
		add_settings_section(
			"bkf_features_section", //id
			__("Other Features","bakkbone-florist-companion"), //title
			array($this,"bkfAddFeaturesInfo"), //callback
			"bkf-options" //page
		);
		add_settings_section(
			"bkf_advanced_section", //id
			__("Advanced Settings","bakkbone-florist-companion"), //title
			array($this,"bkfAddAdvancedInfo"), //callback
			"bkf-options" //page
		);
			
		add_settings_field(
			"card_length", //id
			__("Card Message Length","bakkbone-florist-companion"), //title
			array($this,"bkfCardLengthCallback"), //callback
			"bkf-options", //page
			"bkf_options_section" //section
		);

		add_settings_field(
			"cs_heading", //id
			__("Cross-Sell Cart Heading","bakkbone-florist-companion"), //title
			array($this,"bkfCsHeadingCallback"), //callback
			"bkf-options", //page
			"bkf_options_section" //section
		);
		
		add_settings_field(
			"noship", //id
			__("No-Ship Message","bakkbone-florist-companion"), //title
			array($this,"bkfNoshipCallback"), //callback
			"bkf-options", //page
			"bkf_options_section" //section
		);
		
		add_settings_field(
			"excerpt_pa", //id
			__("Product Archives","bakkbone-florist-companion"), //title
			array($this,"bkfExcerptPaCallback"), //callback
			"bkf-options", //page
			"bkf_features_section" //section
		);
		
		add_settings_field(
			"suburbs_on", //id
			__("Delivery Suburbs","bakkbone-florist-companion"), //title
			array($this,"bkfSuburbsOnCallback"), //callback
			"bkf-options", //page
			"bkf_features_section" //section
		);
		
		add_settings_field(
			"petals_on", //id
			__("Petals Network","bakkbone-florist-companion"), //title
			array($this,"bkfPetalsOnCallback"), //callback
			"bkf-options", //page
			"bkf_features_section" //section
		);
		
		add_settings_field(
			"disable_order_comments", //id
			__("Disable Order Comments","bakkbone-florist-companion"), //title
			array($this,"bkfDOCCallback"), //callback
			"bkf-options", //page
			"bkf_features_section" //section
		);
		
		add_settings_field(
			"deactivation_purge", //id
			__("Purge on Deactivation","bakkbone-florist-companion"), //title
			array($this,"bkfPurgeCallback"), //callback
			"bkf-options", //page
			"bkf_advanced_section" //section
		);
		
	}

	function bkfAddOptionsSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["card_length"]))
			$new_input["card_length"] = sanitize_text_field($input["card_length"]);
		
		if(isset($input["cs_heading"]))
			$new_input["cs_heading"] = sanitize_text_field($input["cs_heading"]);
		
		if(isset($input["noship"]))
			$new_input["noship"] = sanitize_text_field($input["noship"]);
		
		return $new_input;
	}

	function bkfAddFeaturesSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["excerpt_pa"])){
			$new_input["excerpt_pa"] = true;
		}else{
			$new_input["excerpt_pa"] = false;
		}

		if(isset($input["suburbs_on"])){
			$new_input["suburbs_on"] = true;
		}else{
			$new_input["suburbs_on"] = false;
		}
		
		if(isset($input["petals_on"])){
			$new_input["petals_on"] = true;
		}else{
			$new_input["petals_on"] = false;
		}
		
		if(isset($input["disable_order_comments"])){
			$new_input["disable_order_comments"] = true;
		}else{
			$new_input["disable_order_comments"] = false;
		}
		
		return $new_input;
	}

	function bkfAddAdvancedSanitize($input)
	{
		$new_input = array();
		
		if(isset($input["deactivation_purge"])){
			$new_input["deactivation_purge"] = true;
		}else{
			$new_input["deactivation_purge"] = false;
		}
		
		return $new_input;
	}
	
	function bkfAddOptionsInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("Enter your settings below:","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfAddFeaturesInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("The below features are optional.","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfAddAdvancedInfo()
	{
		echo '<p class="bkf-pageinfo">';
		_e("The below settings are more advanced.","bakkbone-florist-companion");
		echo '</p>';
	}
	
	function bkfCardLengthCallback(){
	
		if(isset($this->bkf_options_setting["card_length"])){
			$value = esc_attr($this->bkf_options_setting["card_length"]);
		}else{
			$value = '250';
		}
		?>
		<input class="bkf-form-control small-text" id="bkf-card-length" type="number" name="bkf_options_setting[card_length]" placeholder="250" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Maximum number of characters (including spaces/punctuation) a customer will be able to enter in the Card Message field.","bakkbone-florist-companion") ?></p>
		<?php
	}
	
	function bkfCsHeadingCallback(){
	
		$placeholder = __('How about adding...');
		if(isset($this->bkf_options_setting["cs_heading"])){
			$value = esc_attr($this->bkf_options_setting["cs_heading"]);
		}else{
			$value = $placeholder;
		}
		?>
		<input class="bkf-form-control regular-text" id="bkf-cs-heading" type="text" name="bkf_options_setting[cs_heading]" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Replaces the heading of the Cross-Sells section of the Cart page","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfNoshipCallback(){
		
		$placeholder = __('You have selected a suburb or region we do not deliver to.','bakkbone-florist-companion');
		if(isset($this->bkf_options_setting["noship"])){
			$value = esc_attr($this->bkf_options_setting["noship"]);
		}else{
			$value = $placeholder;
		}
		?>
		<input class="bkf-form-control large-text" id="bkf-noship" type="text" name="bkf_options_setting[noship]" placeholder="<?php echo $placeholder; ?>" value="<?php echo $value; ?>" />
		<p class="description"><?php _e("Displays at checkout if the delivery address' suburb is not serviced.","bakkbone-florist-companion") ?></p>
		<?php
	}

	function bkfExcerptPaCallback(){
	
		if(!isset($this->bkf_features_setting["excerpt_pa"])){
			$this->bkf_features_setting["excerpt_pa"] = false;
		}
		if($this->bkf_features_setting["excerpt_pa"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Display Short Description in product archives","bakkbone-florist-companion") ?><input id="bkf-excerpt-pa" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[excerpt_pa]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfSuburbsOnCallback(){
	
		if(!isset($this->bkf_features_setting["suburbs_on"])){
			$this->bkf_features_setting["suburbs_on"] = true;
		}
		if($this->bkf_features_setting["suburbs_on"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Enable Delivery Suburbs feature (allows you to restrict delivery methods by suburb instead of postcode)","bakkbone-florist-companion") ?><input id="bkf-suburbs-on" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[suburbs_on]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfPetalsOnCallback(){
	
		if(!isset($this->bkf_features_setting["petals_on"])){
			$this->bkf_features_setting["petals_on"] = false;
		}
		if($this->bkf_features_setting["petals_on"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Enable Petals Network Integration","bakkbone-florist-companion") ?><input id="bkf-petals-on" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[petals_on]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfDOCCallback(){
	
		if(!isset($this->bkf_features_setting["disable_order_comments"])){
			$this->bkf_features_setting["disable_order_comments"] = true;
		}
		if($this->bkf_features_setting["disable_order_comments"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Disable the Order Comments field (freetext field at checkout for order notes)","bakkbone-florist-companion") ?><input id="bkf-petals-on" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_features_setting[disable_order_comments]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}

	function bkfPurgeCallback(){
	
		if(!isset($this->bkf_advanced_setting["deactivation_purge"])){
			$this->bkf_advanced_setting["deactivation_purge"] = false;
		}
		if($this->bkf_advanced_setting["deactivation_purge"] == true){
			$checked = "checked";
		}else{
			$checked = "";
		}
		?>
		<label class="bkf-check-container"><?php _e("Purge all data from the database on deactivation","bakkbone-florist-companion") ?><input id="bkf-deactivation-purge" <?php echo $checked ?> type="checkbox" class="bkf-form-control"  name="bkf_advanced_setting[deactivation_purge]" /><span class="bkf-check-checkmark"></span></label>
		<?php
	}
	
	function bkfOptionsAdminFooter()
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_options")
		{
			
		}
	}

	function bkfOptionsAdminEnqueueScripts($hook)
	{
		$screen = get_current_screen();
		if($screen->id == "settings_page_bkf_options")
		{

		}
	}
	
}