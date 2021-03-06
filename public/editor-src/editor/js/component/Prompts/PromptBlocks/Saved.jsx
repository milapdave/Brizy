import React from "react";
import _ from "underscore";
import ScrollPane from "visual/component/ScrollPane";
import DataFilter from "./common/DataFilter";
import { assetUrl } from "visual/utils/asset";
import SearchInput from "./common/SearchInput";
import ThumbnailGrid from "./common/ThumbnailGrid";
import Editor from "visual/global/Editor";
import {
  blockThumbnailData,
  placeholderBlockThumbnailUrl
} from "visual/utils/blocks";
import { getStore } from "visual/redux/store";
import { updateGlobals } from "visual/redux/actionCreators";
import { t } from "visual/utils/i18n";

const defaultFilterUI = {
  search: true
};

export default class Saved extends React.Component {
  static defaultProps = {
    filterUI: defaultFilterUI,
    blocksConfig: null,
    onAddBlocks: _.noop,
    onClose: _.noop
  };

  constructor(props) {
    super(props);

    this.blocksConfig = props.blocksConfig || Editor.getBlocks();
  }

  state = {
    value: ""
  };

  handleThumbnailAdd = thumbnailData => {
    const { onAddBlocks, onClose } = this.props;
    const { resolve } = thumbnailData;

    onAddBlocks(resolve);
    onClose();
  };

  handleThumbnailRemove = thumbnailData => {
    const store = getStore();
    const savedBlocks = store.getState().globals.project.savedBlocks || [];
    const newSavedBlocks = _.without(savedBlocks, thumbnailData.resolve);

    store.dispatch(updateGlobals("savedBlocks", newSavedBlocks));
  };

  renderDataFilter(data) {
    const { HeaderSlotLeft, filterUI } = this.props;
    const filterFn = (item, cf) => {
      const categoryMatch =
        cf.category === "*" || item.cat.includes(Number(cf.category));
      const searchMatch =
        cf.search === "" ||
        new RegExp(cf.search.replace(/[.*+?^${}()|[\]\\]/g, ""), "i").test(
          item.keywords
        );

      return categoryMatch && searchMatch;
    };
    const defaultFilter = {
      category: "*",
      search: ""
    };

    return (
      <DataFilter data={data} filterFn={filterFn} defaultFilter={defaultFilter}>
        {(filteredThumbnails, currentFilter, setFilter) => (
          <React.Fragment>
            {filterUI.search && (
              <HeaderSlotLeft>
                <SearchInput
                  className="brz-ed-popup-two-header__search"
                  value={currentFilter.search}
                  onChange={value => setFilter({ search: value })}
                />
              </HeaderSlotLeft>
            )}
            <div className="brz-ed-popup-two-body__content">
              <ScrollPane
                style={{
                  overflow: "hidden",
                  height: "100%"
                }}
                className="brz-ed-scroll--medium brz-ed-scroll--new-dark"
              >
                {filteredThumbnails.length > 0 ? (
                  <ThumbnailGrid
                    data={filteredThumbnails}
                    onThumbnailAdd={this.handleThumbnailAdd}
                    onThumbnailRemove={this.handleThumbnailRemove}
                  />
                ) : (
                  <div className="brz-ed-popup-two-blocks__grid brz-ed-popup-two-blocks__grid-clear">
                    <p className="brz-ed-popup-two-blocks__grid-clear-text">
                      {t("Nothing here, please refine your search.")}
                    </p>
                  </div>
                )}
              </ScrollPane>
            </div>
          </React.Fragment>
        )}
      </DataFilter>
    );
  }

  renderClear() {
    const { HeaderSlotLeft, filterUI } = this.props;

    return (
      <React.Fragment>
        {filterUI.search && (
          <HeaderSlotLeft>
            <SearchInput
              className="brz-ed-popup-two-header__search"
              value={this.state.value}
              onChange={value => this.setState({ value })}
            />
          </HeaderSlotLeft>
        )}
        <div className="brz-ed-popup-two-body__content">
          <div className="brz-ed-popup-two-blocks__grid brz-ed-popup-two-blocks__grid-clear">
            {this.state.value !== "" ? (
              <p className="brz-ed-popup-two-blocks__grid-clear-text">
                {t("Nothing here, please refine your search.")}
              </p>
            ) : (
              <React.Fragment>
                <p className="brz-ed-popup-two-blocks__grid-clear-text">
                  {t("Nothing here yet, save a block first.")}
                </p>
                <img
                  src={`${assetUrl(
                    "editor/img/save_toolbar.gif"
                  )}?${Math.random()}`}
                  className="brz-ed-popup-two-blocks__grid-clear-image-saved"
                />
              </React.Fragment>
            )}
          </div>
        </div>
      </React.Fragment>
    );
  }

  render() {
    const savedBlocks = getStore().getState().globals.project.savedBlocks || [];
    const thumbnails = savedBlocks.reduce((acc, block) => {
      const blockData = Editor.getBlock(block.blockId);
      let thumbnailData;

      if (!blockData) {
        if (
          !this.blocksConfig.allowMissing ||
          !this.blocksConfig.allowMissing(block)
        ) {
          return acc;
        }

        thumbnailData = {
          thumbnailSrc: placeholderBlockThumbnailUrl(),
          thumbnailWidth: 500,
          thumbnailHeight: 200,
          showRemoveIcon: true,
          keywords: "",
          cat: [],
          type: -1,
          resolve: block
        };
      } else {
        const inCategories =
          this.blocksConfig.categories.find(cat =>
            blockData.cat.includes(cat.id)
          ) !== undefined;
        if (!inCategories) {
          return acc;
        }

        const { keywords, cat } = blockData;
        const { url, width, height } = blockThumbnailData(block);

        thumbnailData = {
          id: block.blockId,
          thumbnailSrc: url,
          thumbnailWidth: width,
          thumbnailHeight: height,
          showRemoveIcon: true,
          keywords,
          cat,
          type: this.id,
          resolve: block
        };
      }

      acc.push(thumbnailData);

      return acc;
    }, []);

    return thumbnails.length > 0
      ? this.renderDataFilter(thumbnails)
      : this.renderClear();
  }
}
