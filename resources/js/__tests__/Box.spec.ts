import 'jest';
import 'jsdom-global'
import { mount } from '@vue/test-utils'
import StatsBoxSimple from '../components/stats-box-simple.vue'

describe('StatsBoxSimple.vue', () => {
    test('renders correctly', () => {
        const wrapper = mount(StatsBoxSimple, {
            propsData: { title:"the title" }
        })
        expect(wrapper.text()).toMatch("the title")
    })
})
