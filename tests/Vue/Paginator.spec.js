import {mount} from '@vue/test-utils';
import expect from 'expect';
import Paginator from '../../resources/assets/js/components/Paginator';

describe('Paginator', () => {
    let wrapper;
    let dataSet = {
        current_page: 1,
        data: [],
        first_page_url: "http://127.0.0.1:8000/threads/nobis/54/replies?page=1",
        from: 1,
        last_page: 10,
        last_page_url: "http://127.0.0.1:8000/threads/nobis/54/replies?page=10",
        next_page_url: "http://127.0.0.1:8000/threads/nobis/54/replies?page=2",
        path: "http://127.0.0.1:8000/threads/nobis/54/replies",
        per_page: 1,
        prev_page_url: null,
        to: 1,
        total: 10
    };

    const expectedPages = [
        { page: 1, pagesArray: [1, 2, 3, 4, 5, 6, 10] },
        { page: 2, pagesArray: [1, 2, 3, 4, 5, 6, 10] },
        { page: 3, pagesArray: [1, 2, 3, 4, 5, 6, 10] },
        { page: 4, pagesArray: [1, 2, 3, 4, 5, 6, 10] },
        { page: 5, pagesArray: [1, 3, 4, 5, 6, 7, 10] },
        { page: 6, pagesArray: [1, 4, 5, 6, 7, 8, 10] },
        { page: 7, pagesArray: [1, 5, 6, 7, 8, 9, 10] },
        { page: 8, pagesArray: [1, 5, 6, 7, 8, 9, 10] },
        { page: 9, pagesArray: [1, 5, 6, 7, 8, 9, 10] },
        { page: 10, pagesArray: [1, 5, 6, 7, 8, 9, 10] }
    ];

    beforeEach(() => {
        wrapper = mount(Paginator);
        wrapper.setData({ dataSet, maxPage: 7 });
    });

    it('returns the correct page numbers as an array', () => {
        for (const obj of expectedPages) {
            const newDataSet = Object.assign({}, dataSet, { current_page: obj.page });
            wrapper.setData({ dataSet: newDataSet });

            expect(wrapper.vm.pagesArray).toEqual(
                obj.pagesArray
            );
        }
    });

    it('disables prev button if no prev url', () => {
        expect(wrapper.find('.prev').hasClass('disabled')).toBe(true);
    });

    it('enables prev button if given prev url', () => {
        wrapper.setData({
            dataSet: Object.assign({}, dataSet, { prev_page_url: 'some://url.here' })
        });

        expect(wrapper.find('.prev').hasClass('disabled')).toBe(false);
    });

    it('disables next button if no next url', () => {
        wrapper.setData({
            dataSet: Object.assign({}, dataSet, {
                next_page_url: null,
                prev_page_url: 'prev://page.here'
            })
        });

        expect(wrapper.find('.next').hasClass('disabled')).toBe(true);
    });

    it('enables next button if given next url', () => {
        expect(wrapper.find('.next').hasClass('disabled')).toBe(false);
    });

    it('display the current page number as active', () => {
        for (const page of [1,2,3,4,5,6,7,8,9,10]) {
            const newDataSet = Object.assign({}, dataSet, { current_page: page });
            wrapper.setData({ dataSet: newDataSet });

            expect(wrapper.find('#page-' + page).hasClass('active')).toBe(true);
        }
    });

    it('disappears if neither prev and next url given', () => {
        wrapper.setData({
            dataSet: Object.assign({}, dataSet, {
                next_page_url: null,
                prev_page_url: null
            })
        });

        expect(wrapper.vm.shouldPaginate).toBe(false);
    });
});