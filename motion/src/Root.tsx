import { Composition } from "remotion";
import { CommercialIndustrial } from "./CommercialIndustrial";
import { Government } from "./Government";
import { Community } from "./Community";
import { EpcPartnership } from "./EpcPartnership";
import { Residential } from "./Residential";

const common = { durationInFrames: 390, fps: 30, width: 1080, height: 1080 };

export const RemotionRoot: React.FC = () => {
  return (
    <>
      <Composition id="CommercialIndustrial" component={CommercialIndustrial} {...common} />
      <Composition id="Government" component={Government} {...common} />
      <Composition id="Community" component={Community} {...common} />
      <Composition id="EpcPartnership" component={EpcPartnership} {...common} />
      <Composition id="Residential" component={Residential} {...common} />
    </>
  );
};
