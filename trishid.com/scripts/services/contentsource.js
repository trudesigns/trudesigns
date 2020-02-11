'use strict';

/**
 * @ngdoc service
 * @name trishApp.Contentsource
 * @description
 * # Contentsource
 * Service in the trishApp.
 */
angular.module('trishApp')
  .service('Contentsource', function Contentsource() {

  var slide_1 =
  '<div class="modal-section"><img src="/images/mywork/uxProjects/overview.png" alt="magento2.0"><h1>OVERVIEW</h1><p>A redesign of a Drupal CMS site and Magento 1.0 that is merged into one Magento responsive 2.0 system.</p></div>'+
  '<div class="modal-section"><img src="/images/mywork/uxProjects/projectGoals.png" alt="project goals"><h1>PROJECT GOALS</h1><p>A few main goals of this project were: to improve usability, new navigation and information architect, card sort product architecture, validate new features, make it responsive for mobile devices, merge two separate websites into one system to simplify backend maintenance.</p></div>'+
  '<div class="modal-section"><img src="/images/mywork/uxProjects/productDiscription.png" alt="product description"><h1>PRODUCT DESCRIPTION</h1><p>Magento Hybrid 2.0 is an informational e-commerce online store with a target audience being dentist, surgeons, and medical practitioners to purchase Implant Directs manufactured dental implants, medical tools, and educational courses to improve their professional medical practice.</p></div>'+
  '<div class="modal-section"><img src="/images/mywork/uxProjects/userResearch.png" alt="user research"><h1>USER RESEARCH</h1><p>I was responsible for managing research with our user base. I collected both quantitative and qualitative data related to usability on both of the existing sites user experience. Research techniques including: usability test, heatmaps, anonymous surveys, interviews with different various department heads, dental practitioners, dental educational speakers and professors, anonymous video recording observations, and google analytics.</p></div>'+
  '<div class="modal-section"><img src="/images/mywork/uxProjects/userAnalysis.png" alt="user analysis"><h1>USER ANALYSIS</h1><p>I started off with defining the scope of the project, the problems, the constraints and some of the potential challenges. Before I started the process I presented to my department and then the stakeholders the low hanging fruits, presented a new sitemap, a few sources of inspiration, and assess some of their design choices and modern design trends to determine whether it would work for our platform. I presented findings of pain points and new features that could help. The about page not being up to date and showing friendly faces would demonstrate our culture when exposed for trade-shows, the education course section not having an interactive calendar cause a lot of support calls, and the home page to have a call to actions and more of a personal message to its target audience would give the audience a better understanding of what the company had to offer. I also discovered the product set up was not user-friendly according to the dentist trying to purchase the dental equipment, and compatible parts was a #1 support call and search on the website which was very hidden and not fully developed.</p></div>'+
  '<div class="modal-section"><img src="/images/mywork/uxProjects/theProcess.png" alt="the process"><h1>THE PROCESS</h1><p>The research I gathered offered key insight and validation to the product roadmap. After presenting my UX analysis I acted as the project manager to facilitate the project road map into "work front" a project management system. I started off with card sorting to get a rough sketch of the new information architecture on the new website navigation and the hierarchy of the product setup which then lead me to put the entire site into a wireframing program called balsamic for the mobile and desktop version then presented the interface design in Invision for a mock up prototype to the team. I then worked in a agile workflow to get the content and the developer to work in sprints to deliver the functioning prototype. I developed a shared folder between the developer and myself to deliver the user interface compressed assets from Adobe Illustrator.</p></div>'+
  '<div class="modal-section"><img src="/images/mywork/uxProjects/nextStep.png" alt="next step"><h1>NEXT STEP</h1><p>In conclusion, the redesign so far met lots of positive reviews, the platform was completely designed from an idea, with UX facts, to real solutions, to sketch, to low fidelity mockup to high fidelity mock-ups, and a complete UI redesign. The platform is currently going to be developed and is looking to launch mid-2018.</p></div>';

    this.data = [
      { title : 'ID Magento Hybrid 2.0', content : slide_1 }
      //{ title : 'SOME TITLE 2', content : 'CONTENT 2' },
      //{ title : 'SOME TITLE 3', content : 'CONTENT 3' },
      //{ title : 'SOME TITLE 4', content : 'CONTENT 4' },
      //{ title : 'SOME TITLE 5', content : 'CONTENT 5' }
    ];

    this.get = function () {
      return this.data;
    };

  });