Array.prototype.remove=function(){for(var t,e=arguments,h=e.length,i;h&&this.length;)for(t=e[--h];(i=this.indexOf(t))!==-1;)this.splice(i,1);return this};
