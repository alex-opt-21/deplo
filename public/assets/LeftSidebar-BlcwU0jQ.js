import{n as e,s as t,t as n}from"./jsx-runtime-C7M7YA1l.js";import{_ as r,h as i,m as a}from"./index-BhXyX60n.js";import{t as o}from"./createLucideIcon-CJMRw7nz.js";import{t as s}from"./bookmark-Bwh5_Ltm.js";import{t as c}from"./briefcase-BngT2mxk.js";import{n as l,r as u,t as d}from"./layout-dashboard-DkXv6KlT.js";import{t as f}from"./search-DzPBMIgl.js";import{t as p}from"./trending-up-CbBfW5-0.js";import{t as m}from"./user-C71dZlC2.js";var h=o(`chevron-down`,[[`path`,{d:`m6 9 6 6 6-6`,key:`qrunsl`}]]),g=o(`chevron-up`,[[`path`,{d:`m18 15-6-6-6 6`,key:`153udz`}]]),_=o(`panel-left-close`,[[`rect`,{width:`18`,height:`18`,x:`3`,y:`3`,rx:`2`,key:`afitv7`}],[`path`,{d:`M9 3v18`,key:`fh3hqa`}],[`path`,{d:`m16 15-3-3 3-3`,key:`14y99z`}]]),v=o(`panel-left-open`,[[`rect`,{width:`18`,height:`18`,x:`3`,y:`3`,rx:`2`,key:`afitv7`}],[`path`,{d:`M9 3v18`,key:`fh3hqa`}],[`path`,{d:`m14 9 3 3-3 3`,key:`8010ee`}]]),y=o(`sliders-horizontal`,[[`path`,{d:`M10 5H3`,key:`1qgfaw`}],[`path`,{d:`M12 19H3`,key:`yhmn1j`}],[`path`,{d:`M14 3v4`,key:`1sua03`}],[`path`,{d:`M16 17v4`,key:`1q0r14`}],[`path`,{d:`M21 12h-9`,key:`1o4lsq`}],[`path`,{d:`M21 19h-5`,key:`1rlt1p`}],[`path`,{d:`M21 5h-7`,key:`1oszz2`}],[`path`,{d:`M8 10v4`,key:`tgpxqk`}],[`path`,{d:`M8 12H3`,key:`a7s4jb`}]]),b=r(),x=t(e(),1),S=n(),C={panel:[{page:`home`,route:`/feed`,icon:u,label:`Inicio`,color:`res-blue`},{page:`dashboard`,route:`/dashboard`,icon:d,label:`Dashboard`,color:`res-violet`},{page:`search`,route:`/search`,icon:f,label:`Buscar`,color:`res-blue`}],actividad:[{page:`guardados`,route:`/guardados`,icon:s,label:`Guardados`,color:`res-teal`},{page:`tendencias`,route:`/tendencias`,icon:p,label:`Tendencias`,color:`res-blue`}]},w=[{filter:`todos`,icon:m,label:`Todos`,color:`res-violet`},{filter:`portafolios`,icon:l,label:`Portafolios`,color:`res-teal`},{filter:`convocatorias`,icon:c,label:`Convocatorias`,color:`res-blue`}],T=[{id:`tipo`,label:`Tipo`,icon:m,options:[`Todos`,`Usuario`,`Proyecto`,`Convocatoria`]},{id:`habilidad`,label:`Habilidad`,icon:l,options:[`Todas`,`React`,`Node.js`,`Python`,`TypeScript`,`Vue`,`Flutter`]},{id:`experiencia`,label:`Experiencia`,icon:c,options:[`Cualquiera`,`Junior`,`Semi-Senior`,`Senior`,`Lead`]},{id:`profesional`,label:`Profesional`,icon:y,options:[`Todos`,`Freelance`,`Tiempo completo`,`Prácticas`]}];function E(e){let t=(0,b.c)(48),{onFilter:n,activeFilter:r,onSearchFilter:o,activeSearchFilters:s}=e,c;t[0]===s?c=t[1]:(c=s===void 0?{}:s,t[0]=s,t[1]=c);let l=c,[u,d]=(0,x.useState)(!1),[f,p]=(0,x.useState)(null),m=i(),y=a(),E=y.pathname===`/nueva-busqueda`,O;t[2]===y.pathname?O=t[3]:(O=Object.values(C).flat().find(e=>e.route===y.pathname)?.page??`home`,t[2]=y.pathname,t[3]=O);let k=O,A;t[4]!==k||t[5]!==u||t[6]!==m?(A=function(e){let{page:t,route:n,icon:r,label:i,sub:a,color:o}=e;return(0,S.jsxs)(`div`,{className:`resource-item${k===t?` active`:``}${u?` collapsed`:``}`,onClick:()=>m(n),title:u?i:``,children:[(0,S.jsx)(`div`,{className:`resource-icon ${o}`,children:(0,S.jsx)(r,{size:18})}),!u&&(0,S.jsxs)(`div`,{className:`resource-text`,children:[(0,S.jsx)(`div`,{className:`resource-label`,children:i}),a&&(0,S.jsx)(`div`,{className:`resource-sub`,children:a})]})]},t)},t[4]=k,t[5]=u,t[6]=m,t[7]=A):A=t[7];let j=A,M;t[8]!==r||t[9]!==u||t[10]!==n?(M=function(e){let{filter:t,icon:i,label:a,color:o}=e;return(0,S.jsxs)(`div`,{className:`resource-item${r===t?` active`:``}${u?` collapsed`:``}`,onClick:()=>n?.(t),title:u?a:``,children:[(0,S.jsx)(`div`,{className:`resource-icon ${o}`,children:(0,S.jsx)(i,{size:15})}),!u&&(0,S.jsx)(`div`,{className:`resource-text`,children:(0,S.jsx)(`div`,{className:`resource-label`,children:a})})]},t)},t[8]=r,t[9]=u,t[10]=n,t[11]=M):M=t[11];let N=M,P;t[12]!==l||t[13]!==u||t[14]!==o||t[15]!==f?(P=function(){return u?T.map(D):T.map(e=>{let{id:t,label:n,icon:r,options:i}=e,a=f===t,s=l[t]||i[0];return(0,S.jsxs)(`div`,{className:`search-filter-group`,children:[(0,S.jsxs)(`div`,{className:`search-filter-header`,onClick:()=>p(a?null:t),children:[(0,S.jsxs)(`div`,{className:`search-filter-header-left`,children:[(0,S.jsx)(r,{size:14,className:`search-filter-icon`}),(0,S.jsx)(`span`,{className:`search-filter-label`,children:n})]}),(0,S.jsxs)(`div`,{className:`search-filter-right`,children:[(0,S.jsx)(`span`,{className:`search-filter-selected`,children:s}),a?(0,S.jsx)(g,{size:13}):(0,S.jsx)(h,{size:13})]})]}),a&&(0,S.jsx)(`div`,{className:`search-filter-options`,children:i.map(e=>(0,S.jsx)(`div`,{className:`search-filter-option${s===e?` selected`:``}`,onClick:()=>{o?.(t,e),p(null)},children:e},e))})]},t)})},t[12]=l,t[13]=u,t[14]=o,t[15]=f,t[16]=P):P=t[16];let F=P,I=`
        .sidebar-left {
          width: ${u?`64px`:`240px`};
          min-width: ${u?`64px`:`240px`};
          transition: width 0.25s ease, min-width 0.25s ease;
          overflow: hidden;
        }

        .sidebar-toggle-btn {
          background: none;
          border: none;
          cursor: pointer;
          padding: 4px;
          border-radius: 6px;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #9ca3af;
          transition: background 0.15s, color 0.15s;
          flex-shrink: 0;
        }
        .sidebar-toggle-btn:hover {
          background: #f3f4f6;
          color: #111827;
        }

        .sidebar-left .panel-card-title {
          display: flex;
          align-items: center;
          justify-content: ${u?`center`:`space-between`};
          overflow: hidden;
          white-space: nowrap;
        }

        .sidebar-left .card-title:not(.panel-card-title) {
          overflow: hidden;
          white-space: nowrap;
          transition: opacity 0.2s, max-height 0.25s;
          opacity: ${u?0:1};
          max-height: ${u?`0px`:`32px`};
          margin-bottom: ${u?`0`:void 0};
        }

        .resource-item.collapsed {
          justify-content: center;
          padding: 8px 0;
        }

        .resource-text {
          overflow: hidden;
          white-space: nowrap;
        }

        /* ── Filtros de búsqueda ── */
        .search-filter-group {
          margin-bottom: 4px;
          border-radius: 8px;
          overflow: hidden;
          border: 1px solid #f0f0f0;
        }

        .search-filter-header {
          display: flex;
          align-items: center;
          justify-content: space-between;
          padding: 8px 10px;
          cursor: pointer;
          background: #fafafa;
          transition: background 0.15s;
          user-select: none;
        }
        .search-filter-header:hover {
          background: #f3f4f6;
        }

        .search-filter-header-left {
          display: flex;
          align-items: center;
          gap: 7px;
        }

        .search-filter-icon {
          color: #6b7280;
          flex-shrink: 0;
        }

        .search-filter-label {
          font-size: 13px;
          font-weight: 600;
          color: #374151;
        }

        .search-filter-right {
          display: flex;
          align-items: center;
          gap: 4px;
          color: #9ca3af;
          font-size: 11px;
        }

        .search-filter-selected {
          font-size: 11px;
          color: #6b7280;
          max-width: 70px;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }

        .search-filter-options {
          background: #fff;
          border-top: 1px solid #f0f0f0;
          padding: 4px 0;
        }

        .search-filter-option {
          padding: 7px 14px;
          font-size: 13px;
          color: #374151;
          cursor: pointer;
          transition: background 0.12s;
          border-radius: 4px;
          margin: 0 4px;
        }
        .search-filter-option:hover {
          background: #f3f4f6;
        }
        .search-filter-option.selected {
          color: #e53935;
          font-weight: 600;
          background: #fff5f5;
        }

        /* Título de filtros cuando está colapsado */
        .search-filters-title {
          overflow: hidden;
          white-space: nowrap;
          transition: opacity 0.2s, max-height 0.25s;
          opacity: ${u?0:1};
          max-height: ${u?`0px`:`32px`};
          margin-bottom: ${u?`0`:void 0};
        }
      `,L;t[17]===I?L=t[18]:(L=(0,S.jsx)(`style`,{children:I}),t[17]=I,t[18]=L);let R;t[19]===u?R=t[20]:(R=!u&&(0,S.jsx)(`span`,{children:`Panel`}),t[19]=u,t[20]=R);let z;t[21]===u?z=t[22]:(z=()=>d(!u),t[21]=u,t[22]=z);let B=u?`Expandir menú`:`Colapsar menú`,V;t[23]===u?V=t[24]:(V=u?(0,S.jsx)(v,{size:16}):(0,S.jsx)(_,{size:16}),t[23]=u,t[24]=V);let H;t[25]!==z||t[26]!==B||t[27]!==V?(H=(0,S.jsx)(`button`,{className:`sidebar-toggle-btn`,onClick:z,title:B,children:V}),t[25]=z,t[26]=B,t[27]=V,t[28]=H):H=t[28];let U;t[29]!==H||t[30]!==R?(U=(0,S.jsxs)(`div`,{className:`panel-card-title card-title`,children:[R,H]}),t[29]=H,t[30]=R,t[31]=U):U=t[31];let W;t[32]===j?W=t[33]:(W=C.panel.map(j),t[32]=j,t[33]=W);let G;t[34]!==U||t[35]!==W?(G=(0,S.jsx)(`div`,{className:`card`,children:(0,S.jsxs)(`div`,{className:`card-body`,children:[U,W]})}),t[34]=U,t[35]=W,t[36]=G):G=t[36];let K;t[37]!==E||t[38]!==N||t[39]!==j||t[40]!==F?(K=E?(0,S.jsx)(`div`,{className:`card`,children:(0,S.jsxs)(`div`,{className:`card-body`,children:[(0,S.jsx)(`div`,{className:`card-title search-filters-title`,children:`Filtros`}),F()]})}):(0,S.jsxs)(S.Fragment,{children:[(0,S.jsx)(`div`,{className:`card`,children:(0,S.jsxs)(`div`,{className:`card-body`,children:[(0,S.jsx)(`div`,{className:`card-title`,children:`Explorar`}),w.map(N)]})}),(0,S.jsx)(`div`,{className:`card`,children:(0,S.jsxs)(`div`,{className:`card-body`,children:[(0,S.jsx)(`div`,{className:`card-title`,children:`Actividad`}),C.actividad.map(j)]})})]}),t[37]=E,t[38]=N,t[39]=j,t[40]=F,t[41]=K):K=t[41];let q;t[42]!==G||t[43]!==K?(q=(0,S.jsxs)(`div`,{className:`sidebar-left`,children:[G,K]}),t[42]=G,t[43]=K,t[44]=q):q=t[44];let J;return t[45]!==q||t[46]!==L?(J=(0,S.jsxs)(S.Fragment,{children:[L,q]}),t[45]=q,t[46]=L,t[47]=J):J=t[47],J}function D(e){let{id:t,icon:n}=e;return(0,S.jsx)(`div`,{className:`resource-item collapsed`,title:t,children:(0,S.jsx)(`div`,{className:`resource-icon res-blue`,children:(0,S.jsx)(n,{size:16})})},t)}export{y as n,h as r,E as t};