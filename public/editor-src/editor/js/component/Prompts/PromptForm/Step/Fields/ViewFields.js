import React, { Component } from "react";
import _ from "underscore";
import ScrollPane from "visual/component/ScrollPane";
import Select from "visual/component/Controls/Select";
import SelectItem from "visual/component/Controls/Select/SelectItem";
import Button from "../../Components/Button";
import { isMaxFields } from "../../utils";

class ViewFields extends Component {
  static defaultProps = {
    id: "",
    title: "",
    shortTitle: "",
    description: "",
    img: "",
    form: {},
    formFields: [],

    usedAccount: "",
    accounts: [],

    usedList: "",
    lists: [],

    fieldsMap: "",
    fieldsList: [],
    error: null,
    nextLoading: false,
    prevLoading: false,
    handlePrev: _.noop,
    handleNext: _.noop
  };

  renderSelect(id, target) {
    const { formFields, fields, restrictions, handleActive } = this.props;
    const busyFields = _.pluck(formFields, "target");
    let newFields = fields.filter(item => {
      return busyFields.indexOf(item.slug) === -1 || item.slug === target;
    });

    const allBusyFields =
      fields.length +
      (formFields.length - _.without(busyFields, "_auto_generate").length);

    if (
      !(
        isMaxFields(allBusyFields, restrictions) &&
        (!target || target !== "_auto_generate")
      )
    ) {
      newFields.unshift({
        name: "Auto Generate",
        required: false,
        slug: "_auto_generate"
      });
    }

    const options = newFields.map(({ required, name, slug }) => {
      return (
        <SelectItem key={slug} value={slug}>
          <span className="brz-span">{name}</span>
          {required && <strong className="brz-strong">*</strong>}
        </SelectItem>
      );
    });

    return (
      <Select
        defaultValue={target}
        className="brz-control__select--white"
        maxItems="6"
        itemHeight="30"
        inPortal={true}
        onChange={itemTarget => {
          handleActive(id, itemTarget);
        }}
      >
        {options}
      </Select>
    );
  }

  renderOptions() {
    const options = this.props.formFields.map(
      ({ sourceTitle, target, sourceId }) => {
        return (
          <div
            key={sourceId}
            className="brz-ed-popup-integrations-step__fields-option"
          >
            <p className="brz-p">{sourceTitle}</p>
            <div className="brz-ed-popup-integrations-step__fields-select">
              {this.renderSelect(sourceId, target)}
            </div>
          </div>
        );
      }
    );

    return (
      <ScrollPane
        style={{ maxHeight: 255 }}
        className="brz-ed-scroll-pane brz-ed-popup-integrations__scroll-pane"
      >
        {options}
      </ScrollPane>
    );
  }

  renderError() {
    return (
      <div className="brz-ed-alert brz-ed-alert-error">
        <span className="brz-span">{this.props.error}</span>
      </div>
    );
  }

  renderErrorEmpty() {
    return (
      <div className="brz-ed-alert brz-ed-alert-error">
        <span className="brz-span">
          Fields are empty. Please add fields and try again.
        </span>
      </div>
    );
  }

  render() {
    const {
      title,
      error,
      formFields,
      prevLoading,
      nextLoading,
      handlePrev,
      handleNext
    } = this.props;

    return (
      <div className="brz-ed-popup-integrations-step brz-ed-popup-integrations-step__fields">
        {error && this.renderError()}
        <div className="brz-ed-popup-integrations-step__head">
          <p className="brz-p">
            <strong className="brz-strong">FORM FIELDS</strong>
          </p>
          <p className="brz-p">
            <strong className="brz-strong">{title} FIELDS</strong>
          </p>
        </div>
        <div className="brz-ed-popup-integrations-step__body">
          {formFields.length ? this.renderOptions() : this.renderErrorEmpty()}
          <div className="brz-ed-popup-integrations-step__buttons">
            <Button
              type="gray"
              leftIcon="nc-arrow-left"
              loading={prevLoading}
              onClick={handlePrev}
            >
              Back
            </Button>
            <Button
              type="tail"
              rightIcon="nc-arrow-right"
              loading={nextLoading}
              onClick={handleNext}
            >
              Continue
            </Button>
          </div>
        </div>
      </div>
    );
  }
}

export default ViewFields;
