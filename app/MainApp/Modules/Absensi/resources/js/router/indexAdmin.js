import globals from "@/globals";
import BlankRouterContainer from '@/layout/BlankRouterContainer';

const Page1Sub1 = resolve => {
  require.ensure(['../views/Page1Sub1'], () => {
      resolve(require('../views/Page1Sub1'));
  });
};

const Page1Sub2Sub1 = resolve => {
  require.ensure(['../views/Page1Sub2Sub1'], () => {
      resolve(require('../views/Page1Sub2Sub1'));
  });
};
const Page1Sub3 = resolve => {
  require.ensure(['../views/Page1Sub3'], () => {
      resolve(require('../views/Page1Sub3'));
  });
};

const Page2 = resolve => {
  require.ensure(['../views/Page2'], () => {
      resolve(require('../views/Page2'));
  });
};

export default [
  {
    path: globals().AppConfig.endpoint.admin.Example,
    component: () => import('@/layout/' + globals().AppConfig.system.web_admin.layout),
    children: [
      {
        path: 'page1',
        component: BlankRouterContainer,
        name: 'example.page1',
        children:[          
            {
              path: 'sub1',
              component: Page1Sub1,
              name: 'example.page1.sub1'
            },
            {
              path: 'sub2/sub1',
              component: BlankRouterContainer,
              name: 'example.page1.sub2',
              children: [
                {
                  path: 'sub1',
                  component: Page1Sub2Sub1,
                  name: 'example.page1.sub2.sub1'
                }
              ]
            },
            {
              path: 'sub3',
              component: Page1Sub3,
              name: 'example.page1.sub3'
            }
        ]
      },
      {
        path: 'page2',
        component: Page2,
        name: 'example.page2'
      }
    ]
  }
];
