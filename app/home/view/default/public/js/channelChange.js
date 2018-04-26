/*图层变幻*/
function setTab_zxjy(name,startnum,endnum,cursel,obj)
  {
		obj.t=setTimeout(function()
		{
			for(i=startnum;i<=endnum;i++)
			{
				var menu=document.getElementById(name+i.toString());
				var con=document.getElementById("con_"+name+"_"+i.toString());
				
                
                if(menu != null)
                {
				   menu.className=i==cursel?"c06":"c05";
				}
				if(con != null)
				{
				   
				   if(i == cursel)
				   {
				      
				      con.style.display="block";
				      
				   }
				   else
				   {
				       con.style.display="none";
				   }
				}
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function()
	    {
	       clearTimeout(this.t);
	    }
	}

	function setTab(name,startnum,endnum,cursel,obj){
		obj.t=setTimeout(function(){
			for(i=startnum;i<=endnum;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("con_"+name+"_"+i);
				var more=document.getElementById("more_"+name+"_"+i);
				menu.className=i==cursel?"table12 float_left":"table13 float_left";
				con.style.display=i==cursel?"block":"none";
				more.style.display=i==cursel?"block":"none";
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function(){clearTimeout(this.t)}
	}
	
	
		function setTab1(name,startnum,endnum,cursel,obj){
		obj.t=setTimeout(function(){
			for(i=startnum;i<=endnum;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("con_"+name+"_"+i);
				menu.className=i==cursel?"bg22 float_left":"bg23 float_left";
				con.style.display=i==cursel?"block":"none";
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function(){clearTimeout(this.t)}
	}
	
	
	function setTab2(name,startnum,endnum,cursel,obj){
		obj.t=setTimeout(function(){
			for(i=startnum;i<=endnum;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("con_"+name+"_"+i);
				menu.className=i==cursel?"float_left yszx_bg02":"float_left yszx_bg03";
				con.style.display=i==cursel?"block":"none";
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function(){clearTimeout(this.t)}
	}
	
	
	function setTab3(name,startnum,endnum,cursel,obj){
		obj.t=setTimeout(function(){
			for(i=startnum;i<=endnum;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("con_"+name+"_"+i);
				menu.className=i==cursel?"float_left wsbs_bg02":"float_left wsbs_bg03";
				con.style.display=i==cursel?"block":"none";
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function(){clearTimeout(this.t)}
	}
	
	
	function setTab4(name,startnum,endnum,cursel,obj){
		obj.t=setTimeout(function(){
			for(i=startnum;i<=endnum;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("con_"+name+"_"+i);
				menu.className=i==cursel?"zwgk_bg02":"zwgk_bg03";
				con.style.display=i==cursel?"block":"none";
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function(){clearTimeout(this.t)}
	}
	
	function setTab5(name,startnum,endnum,cursel,obj){
		obj.t=setTimeout(function(){
			for(i=startnum;i<=endnum;i++){
				var menu=document.getElementById(name+i);
				var con=document.getElementById("con_"+name+"_"+i);
				menu.className=i==cursel?"gzhd_bg07":"gzhd_bg08";
				con.style.display=i==cursel?"block":"none";
			}
		},0)
		//当鼠标在规定时间内移开时，停止切换
		obj.onmouseout=function(){clearTimeout(this.t)}
	}
	