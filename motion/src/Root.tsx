import { Composition } from "remotion";
import { CommercialIndustrial } from "./CommercialIndustrial";
import { Government } from "./Government";

const common = { durationInFrames: 390, fps: 30, width: 1080, height: 1080 };

export const RemotionRoot: React.FC = () => {
  return (
    <>
      <Composition id="CommercialIndustrial" component={CommercialIndustrial} {...common} />
      <Composition id="Government" component={Government} {...common} />
    </>
  );
};
